<?php

namespace App\Http\Controllers;

use App\Models\Battle;
use App\Models\PlanetarySystem;
use App\Models\Ship;
use App\Models\Ressources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BattleController extends Controller
{
    public function create(Request $request)
    {
        $attacker_id = Auth::user()->id;
        $x1 = PlanetarySystem::select('x_coord')->where('user_id', $attacker_id)->first();
        $y1 = PlanetarySystem::select('y_coord')->where('user_id', $attacker_id)->first();

        $defender_id = $request->user_id;
        $x2 = PlanetarySystem::select('x_coord')->where('user_id', $defender_id)->first();
        $y2 = PlanetarySystem::select('y_coord')->where('user_id', $defender_id)->first();

        $resources_looted = Battle::where('user_id', $attacker_id)->first();
        $ttl_att_pts = 0;
        $ttl_def_pts = 0;

        $fuel = Ressources::select('quantity')->where('user_id', $attacker_id)->where('type', 'fuel')->first();

        $fighter = Ship::where('type', 'fighter')->first();
        $consoFighter = 1;
        $frigate = Ship::where('type', 'frigate')->first();
        $consoFrigate = 2;
        $cruiser = Ship::where('type', 'cruiser')->first();
        $consoCruiser = 4;
        $destroyer = Ship::where('type', 'destroyer')->first();
        $consoDestroyer = 8;

        // Helper function to calculate distance between two coordinates
        function calculateDistance($x1, $y1, $x2, $y2)
        {
            return sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));
        }

        if ($fighter) {
            $fuelNeeded = (calculateDistance($x1->x_coord, $y1->y_coord, $x2->x_coord, $y2->y_coord) * $consoFighter) / 10;
        }
        if ($frigate) {
            $fuelNeeded = (calculateDistance($x1->x_coord, $y1->y_coord, $x2->x_coord, $y2->y_coord) * $consoFrigate) / 10;
        }
        if ($cruiser) {
            $fuelNeeded = (calculateDistance($x1->x_coord, $y1->y_coord, $x2->x_coord, $y2->y_coord) * $consoCruiser) / 10;
        }
        if ($destroyer) {
            $fuelNeeded = (calculateDistance($x1->x_coord, $y1->y_coord, $x2->x_coord, $y2->y_coord) * $consoDestroyer) / 10;
        }

        if ($fuelNeeded >= $fuel->quantity) {
            return response()->json(['message' => 'Not enough fuel for the journey.'], 400);
        }

        // Deduct the consumed fuel
        $fuelConsumed = $fuel->quantity - $fuelNeeded;
        $fuel->quantity = $fuelConsumed;
        $fuel->save();

        // Battle outcome calculation
        $battleOutcome = $this->battleRounds($attacker_id, $defender_id);

        // Save the battle result
        $battle = new Battle();
        $battle->attacker_id = $attacker_id;
        $battle->defender_id = $defender_id;
        $battle->resources_looted = $resources_looted->resources * 0.1; // 10% of resources looted
        $battle->battle_result = $battleOutcome;
        $battle->save();

        return response()->json(['message' => 'Battle created successfully.'], 200);
    }

    public function read()
    {
        $user_id = Auth::user()->id;
        $showbattle = Battle::where('user_id', $user_id)->get();
        return response()->json($showbattle, 200);
    }

    // Helper function to implement battle rounds and determine winner
    private function battleRounds($attacker_id, $defender_id)
    {
        // Fetch ships for the attacker and defender
        $attackerShips = Ship::where('user_id', $attacker_id)->get();
        $defenderShips = Ship::where('user_id', $defender_id)->get();

        // Initialize variables to keep track of attacker and defender damage
        $attackerDamage = 0;
        $defenderDamage = 0;

        // Helper function to calculate damage for a given ship type
        $calculateDamage = function ($ship, $type) {
            return $ship->quantity * ($ship->$type * rand(0.5, 1.5));
        };

        // Calculate total attacker damage
        if ($attackerShips->isNotEmpty()) {
            $attackerDamage += $calculateDamage($attackerShips->where('type', 'fighter')->first(), 'attack_points');
            $attackerDamage += $calculateDamage($attackerShips->where('type', 'frigate')->first(), 'attack_points');
            $attackerDamage += $calculateDamage($attackerShips->where('type', 'cruiser')->first(), 'attack_points');
            $attackerDamage += $calculateDamage($attackerShips->where('type', 'destroyer')->first(), 'attack_points');
        }

        // Calculate total defender damage
        if ($defenderShips->isNotEmpty()) {
            $defenderDamage += $calculateDamage($defenderShips->where('type', 'fighter')->first(), 'attack_points');
            $defenderDamage += $calculateDamage($defenderShips->where('type', 'frigate')->first(), 'attack_points');
            $defenderDamage += $calculateDamage($defenderShips->where('type', 'cruiser')->first(), 'attack_points');
            $defenderDamage += $calculateDamage($defenderShips->where('type', 'destroyer')->first(), 'attack_points');
        }

        // Battle logic
        // Determine the winner based on total damage
        if ($attackerDamage > $defenderDamage) {
            return 'attacker';
        } elseif ($attackerDamage < $defenderDamage) {
            return 'defender';
        } else {
            return 'draw';
        }
    }
}
