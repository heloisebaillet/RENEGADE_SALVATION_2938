<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BattleController extends Controller
{
    /**
     * Create a new battle between an attacker and a defender.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'attacker_id' => 'required|integer',
            'defender_id' => 'required|integer',
            'attacker_ships' => 'required|array',
            'defender_ships' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid data'], 400);
        }

        $attackerId = $request->input('attacker_id');
        $defenderId = $request->input('defender_id');
        $attackerShips = $request->input('attacker_ships');
        $defenderShips = $request->input('defender_ships');

        // Calculate the total attack points for the attacker
        $attackerAttackPoints = $this->calculateAttackPoints($attackerShips);

        // Calculate the total defense points for the defender
        $defenderDefensePoints = $this->calculateDefensePoints($defenderShips);

        // Start the battle and calculate the results for each round
        $rounds = [];
        while (!empty($attackerShips) && !empty($defenderShips)) {
            // Calculate the total attack points for the attacker in this round
            $roundAttackerAttackPoints = $this->calculateAttackPoints($attackerShips);

            // Calculate the total defense points for the defender in this round
            $roundDefenderDefensePoints = $this->calculateDefensePoints($defenderShips);

            // Determine the winner of this round
            if ($roundAttackerAttackPoints > $roundDefenderDefensePoints) {
                // Attacker wins this round
                $this->destroyShips($defenderShips);
            } elseif ($roundAttackerAttackPoints < $roundDefenderDefensePoints) {
                // Defender wins this round
                $this->destroyShips($attackerShips);
            } else {
                // Draw, both sides lose 30% of their ships
                $this->destroyShips($attackerShips, 0.3);
                $this->destroyShips($defenderShips, 0.3);
            }

            // Save the round results
            $rounds[] = [
                'attacker_ships' => $attackerShips,
                'defender_ships' => $defenderShips,
            ];
        }

        // Determine the battle result
        $battleResult = null;
        if (empty($attackerShips) && empty($defenderShips)) {
            $battleResult = 'Draw';
        } elseif (empty($attackerShips)) {
            $battleResult = 'Defender wins';
        } else {
            $battleResult = 'Attacker wins';
        }

        // Save the battle results in the battles table
        $this->saveBattleResults($attackerId, $defenderId, $attackerAttackPoints, $defenderDefensePoints, $battleResult);

        // Update the ships for the attacker and defender based on the battle results
        $this->updateShips($attackerId, $attackerShips);
        $this->updateShips($defenderId, $defenderShips);

        // Return the battle results
        return response()->json(['result' => $battleResult, 'rounds' => $rounds]);
    }

    // Calculate the total attack points for a given set of ships
    private function calculateAttackPoints($ships)
    {
        $totalAttackPoints = 0;

        foreach ($ships as $ship) {
            // Assume $ship['quantity'] contains the number of ships of this type
            // $ship['attack_points'] contains the attack points for this type of ship
            $totalAttackPoints += $ship['quantity'] * $ship['attack_points'] * $this->generateRandomFactor();
        }

        return $totalAttackPoints;
    }

    // Calculate the total defense points for a given set of ships
    private function calculateDefensePoints($ships)
    {
        $totalDefensePoints = 0;

        foreach ($ships as $ship) {
            // Assume $ship['quantity'] contains the number of ships of this type
            // $ship['defense_points'] contains the defense points for this type of ship
            $totalDefensePoints += $ship['quantity'] * $ship['defense_points'] * $this->generateRandomFactor();
        }

        return $totalDefensePoints;
    }

    // Destroy a given percentage of ships from a set of ships
    private function destroyShips(&$ships, $percentage = 0.3)
    {
        // Sort ships by strength (attack or defense points)
        usort($ships, function ($a, $b) {
            return ($b['attack_points'] + $b['defense_points']) <=> ($a['attack_points'] + $a['defense_points']);
        });

        // Calculate the number of ships to destroy based on the percentage
        $numShipsToDestroy = ceil(count($ships) * $percentage);

        // Destroy the weakest ships
        array_splice($ships, 0, $numShipsToDestroy);
    }

    // Save the battle results in the battles table
    private function saveBattleResults($attackerId, $defenderId, $attackerAttackPoints, $defenderDefensePoints, $battleResult)
    {
        DB::table('battles')->insert([
            'attacker_id' => $attackerId,
            'defender_id' => $defenderId,
            'ttl_att_pts' => $attackerAttackPoints,
            'ttl_def_pts' => $defenderDefensePoints,
            'battle_result' => $battleResult,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Update the ships for a given player based on the battle results
    private function updateShips($userId, $updatedShips)
    {
        // Assume ships are already in the correct format with 'type', 'quantity', 'attack_points', 'defense_points'

        // Clear the existing ships for the user
        DB::table('ships')->where('user_id', $userId)->delete();

        // Insert the updated ships
        foreach ($updatedShips as $ship) {
            DB::table('ships')->insert([
                'user_id' => $userId,
                'type' => $ship['type'],
                'quantity' => $ship['quantity'],
                'attack_points' => $ship['attack_points'],
                'defense_points' => $ship['defense_points'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // Generate a random factor between 0.5 and 1.5
    private function generateRandomFactor()
    {
        return mt_rand(5, 15) / 10;
    }
}
