<?php

namespace App\Http\Controllers;

use App\Models\Battle;
use App\Models\ships;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

//use Illuminate\Support\Facades\Auth;

class BattleController extends Controller
{
    public function create(Request $request)
    {
        $user_id = Auth::user()->id;
        $attacker = Battle::class()->user_id;
        $defender = PlanetarySystem::class()->user_id;
        // $enough = "carburant nécessaire pour voyager entre deux systèmes (formule calcul)";
        $distance = calculateDistance($x1, $y1, $x2, $y2);
        $fuelConsumption = calculateFuel();
        $attackPoints = "";
        $defensePoints = "";
        $ressources = Ressources::class()->type;
        $fuelConsumptionRates = [
            'fighter' => 1,
            'frigate' => 2,
            'cruiser' => 4,
            'destroyer' => 8,
        ];
        $userShips = [
            'x' => BattleController::class($attacker)->user_id,
            'y' => BattleController::class($attacker)->user_id,
            'ships' => [
                'fighter' => Ships::class()->type,
                'frigate' => Ships::class()->type,
                'cruiser' => Ships::class()->type,
                'destroyer' => Ships::class()->type,
            ],
            'fuel' => Ressources::class()->type,
        ];
        $targetSystem = [
            'x' => BattleController::class($defender)->user_id,
            'y' => BattleController::class($defender)->user_id,
        ];
        
        function calculateDistance($x1, $y1, $x2, $y2) {
            return sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));
        }

        function hasEnoughFuel($userShips, $targetSystem, $fuelConsumptionRates) {
            
            $distance = calculateDistance($userShips['x'], $userShips['y'], $targetSystem['x'], $targetSystem['y']);
            $totalFuelRequired = "";

            foreach ($userShips['ships'] as $shipType => $quantity) {
                if (isset($fuelConsumptionRates[$shipType])) {
                    $fuelPer10Cases = $fuelConsumptionRates[$shipType];
                    $fuelRequired = ($quantity / 10) * $fuelPer10Cases;
                    $totalFuelRequired += $fuelRequired;
                }
            }
            $userFuel = $userShips['fuel'];
            if ($userFuel >= $totalFuelRequired) {
                return true;
            } else {
                return false;
            }
        }

        function battleRounds($userShips, $enemyShips) {
            
            while (true) {
                usort($userShips, function ($a, $b) {
                    return $a['defensePoints'] - $b['defensePoints'];
                });
                usort($enemyShips, function ($a, $b) {
                    return $a['defensePoints'] - $b['defensePoints'];
                });
        
                $roundDamageUser = '';
                $roundDamageEnemy = '';
        
                foreach ($userShips as &$ship) {
                    $damage = $ship['attackPoints'];
                    foreach ($enemyShips as &$enemyShip) {
                        if ($damage <= 0) break;
        
                        $defense = &$enemyShip['defensePoints'];
                        if ($damage >= $defense) {
                            $roundDamageUser += $defense;
                            $damage -= $defense;
                            $defense = 0;
                        } else {
                            $roundDamageUser += $damage;
                            $defense -= $damage;
                            $damage = 0;
                        }
                    }
                }
        
                foreach ($enemyShips as &$ship) {
                    $damage = $ship['attackPoints'];
                    foreach ($userShips as &$userShip) {
                        if ($damage <= 0) break;
        
                        $defense = &$userShip['defensePoints'];
                        if ($damage >= $defense) {
                            $roundDamageEnemy += $defense;
                            $damage -= $defense;
                            $defense = 0;
                        } else {
                            $roundDamageEnemy += $damage;
                            $defense -= $damage;
                            $damage = 0;
                        }
                    }
                }
        
                $userDamage += $roundDamageUser;
                $enemyDamage += $roundDamageEnemy;
                $userRemainingShips = array_filter($userShips, function ($ship) {
                    return $ship['defensePoints'] > 0;
                });
                $enemyRemainingShips = array_filter($enemyShips, function ($ship) {
                    return $ship['defensePoints'] > 0;
                });
        
                if (empty($userRemainingShips) || empty($enemyRemainingShips)) {
                    break;
                }
            }
        
            if ($userDamage > $enemyDamage) {
                return 'You win!'; 
            } elseif ($userDamage < $enemyDamage) {
                return 'You lose!';
            } else {
                return "It's a draw...";
            }

            $userShips = [
                ['type' => 'fighter', 'attackPoints' => 7, 'defencePoints' => 11],
                ['type' => 'frigate', 'attackPoints' => 13, 'defencePoints' => 5],
                ['type' => 'cruiser', 'attackPoints' => 14, 'defencePoints' => 9],
                ['type' => 'destroyer', 'attackPoints' => 27, 'defencePoints' => 20]
            ];
            
            $enemyShips = [
                ['type' => 'fighter', 'attackPoints' => 7, 'defencePoints' => 11],
                ['type' => 'frigate', 'attackPoints' => 13, 'defencePoints' => 5],
                ['type' => 'cruiser', 'attackPoints' => 14, 'defencePoints' => 9],
                ['type' => 'destroyer', 'attackPoints' => 27, 'defencePoints' => 20]
            ];
            
            $winner = battleRounds($userShips, $enemyShips);
            
            if ($winner === 'user') {
                // User wins
            } elseif ($winner === 'enemy') {
                // Enemy wins
            } else {
                // It's a draw
            }
            
        }

        function winner (user_id ships ressources planetary_system attacker defender)
        {
            if winner == attacker : gain 10% from planetary_system ressources defender = $resources_looted
        }

        function loser (ships){
            30% des ships api.destroy ($ships->quantity * 0.7) restant après battle delete
        }

        if (hasEnoughFuel() == 'true') {

            $battle = new Battle();
            $battle->attacker_id = $attacker_id; 
            $battle->defender_id = $defender_id; 
            $battle->winner_id = $winner_id; 
            $battle->resources_looted = $resources_looted; 
            $battle->save();
                return Response()->json('Attacked Launched!');
            }
            else {
                return Response()->json("You don't have enough fuel to launch an attack. Selecter fewer ships, or produce more fuel");
            }
    }
}