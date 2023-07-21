<?php

namespace App\Http\Controllers;

use App\Models\Battle;
use App\Models\PlanetarySystem;
use App\Models\Ships;
use App\Models\Ressources;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BattleController extends Controller
{
    public function create(Request $request)
    {
        $distance = calculateDistance($x1, $y1, $x2, $y2);
        $user_id = Auth::user()->id;
        $attacker_id = Battle::class()->user_id;
        $defender_id = Battle::class()->user_id;
        $winner_id = Battle::class()->user_id;
        $resources_looted = Battle::class()->user_id;
        $ressources = Ressources::class()->type;

        // on créé l'attaque si toutes les conditions recquises sont validées
        if (hasEnoughFuel($userShips, $targetSystem, $fuelConsumptionRates, $distance, $attacker, $defender, $fuelConsumption)) {
            $battle = new Battle();
            $battle->attacker_id = $attacker_id;
            $battle->defender_id = $defender_id;
            $battle->winner_id = $winner_id;
            $battle->resources_looted = $resources_looted;
            $battle->save();
            return Response()->json('Attack Launched!');
        } else {
            return Response()->json("You don't have enough fuel to launch an attack. Select fewer ships or produce more fuel.");
        }

        // calcul de la distance entre le système planétaire de l'attaquant et du défenseur
        function calculateDistance(&$x1, &$y1, &$x2, &$y2)
        {
            return sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));
        }

        // calcul de la quantité de fuel nécessaire pour parcourir la $distance
        function calculateFuel($userShips, $fuelConsumptionRates)
        {
            $totalFuelRequired = "";

            // on calcule le fuel nécessaire pour chaque type de vaisseau
            // dont la consommation de fuel diffère pour parcourir 10 cases de la map
            foreach ($userShips['ships'] as $shipType => $quantity) {
                if (isset($fuelConsumptionRates[$shipType])) {
                    $fuelPer10Cases = $fuelConsumptionRates[$shipType];
                    $fuelRequired = ($quantity / 10) * $fuelPer10Cases;
                    $totalFuelRequired += $fuelRequired;
                }
            }
            return $totalFuelRequired;
        }

        // on vérifie que le joueur attaquant a le fuel nécessaire pour 
        // atteindre le système ennemi 
        function hasEnoughFuel(&$userShips, &$targetSystem, &$fuelConsumptionRates, &$distance, &$attacker, &$defender, &$fuelConsumption)
        {
            $userShips = [
                'x' => $attacker->user_id,
                'y' => $attacker->user_id,
                'ships' => ['fighter' => Ships::class()->type, 'frigate' => Ships::class()->type, 'cruiser' => Ships::class()->type, 'destroyer' => Ships::class()->type], 'fuel' => Ressources::class()->type
            ];

            $targetSystem = [
                'x' => $defender->user_id,
                'y' => $defender->user_id,
            ];

            $fuelConsumptionRates = ['fighter' => 1, 'frigate' => 2, 'cruiser' => 4, 'destroyer' => 8];
            $distance = calculateDistance($userShips['x'], $userShips['y'], $targetSystem['x'], $targetSystem['y']);
            $attacker = Battle::class()->user_id;
            $defender = PlanetarySystem::class()->user_id;
            $totalFuelRequired = "";
            $fuelConsumption = calculateFuel($userShips, $fuelConsumptionRates);

            foreach ($userShips['ships'] as $shipType => $quantity) {
                if (isset($fuelConsumptionRates[$shipType])) {
                    $fuelPer10Cases = $fuelConsumptionRates[$shipType];
                    $fuelRequired = ($quantity / 10) * $fuelPer10Cases;
                    $totalFuelRequired += $fuelRequired;
                }

                $userFuel = $userShips['fuel'];

                if ($userFuel >= $totalFuelRequired) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        // on lance une boucle des rounds pour déterminer les points d'attaques qui infligent 
        // des dégâts sur les points de défense des flottes 
        function battleRounds(&$userShips, &$enemyShips, &$attackPoints, &$defensePoints)
        {
            $userDamage = "";
            $enemyDamage = "";
            $attackPoints = "";
            $defensePoints = "";

            while (true) {

                // on demande à ce que les vaisseaux les plus faibles soient visés en premier
                usort($userShips, function ($a, $b) {
                    return $a['defensePoints'] - $b['defensePoints'];
                });
                usort($enemyShips, function ($a, $b) {
                    return $a['defensePoints'] - $b['defensePoints'];
                });

                // on indique les points d'attaque et de défense des vaisseaux de l'attaquant
                $userShips = [
                    ['type' => 'fighter', 'attackPoints' => 7, 'defensePoints' => 11],
                    ['type' => 'frigate', 'attackPoints' => 13, 'defensePoints' => 5],
                    ['type' => 'cruiser', 'attackPoints' => 14, 'defensePoints' => 9],
                    ['type' => 'destroyer', 'attackPoints' => 27, 'defensePoints' => 20]
                ];

                // foreach ($userShips['ships'] as $shipType => $quantity) {

                //     if ($shipType === 'fighter') {
                //         $attackPoints = 7;
                //         $defensePoints = 11;
                //     } elseif ($shipType === 'frigate') {
                //         $attackPoints = 13;
                //         $defensePoints = 5;
                //     } elseif ($shipType === 'cruiser') {
                //         $attackPoints = 14;
                //         $defensePoints = 9;
                //     } elseif ($shipType === 'destroyer') {
                //         $attackPoints = 27;
                //         $defensePoints = 20;
                //     }
                // }

                // on indique les points d'attaque et de défense des vaisseaux du défenseur
                $enemyShips = [
                    ['type' => 'fighter', 'attackPoints' => 7, 'defensePoints' => 11],
                    ['type' => 'frigate', 'attackPoints' => 13, 'defensePoints' => 5],
                    ['type' => 'cruiser', 'attackPoints' => 14, 'defensePoints' => 9],
                    ['type' => 'destroyer', 'attackPoints' => 27, 'defensePoints' => 20]
                ];

                // foreach ($enemyShips['ships'] as $shipType => $quantity) {
                //     if ($shipType === 'fighter') {
                //         $attackPoints = 7;
                //         $defensePoints = 11;
                //     } elseif ($shipType === 'frigate') {
                //         $attackPoints = 13;
                //         $defensePoints = 5;
                //     } elseif ($shipType === 'cruiser') {
                //         $attackPoints = 14;
                //         $defensePoints = 9;
                //     } elseif ($shipType === 'destroyer') {
                //         $attackPoints = 27;
                //         $defensePoints = 20;
                //     }
                // }

                // on lance une boucle pour attaquer jusqu'à ce qu'une des deux
                // flottes ait la défense de tous ses vaisseaux à 0
                $roundDamageUser = '';
                $roundDamageEnemy = '';

                foreach ($userShips as &$ship) {

                    // $quantity = $ship['quantity'];
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

                    // $quantity = $ship['quantity'];
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

            $winner = battleRounds($userShips, $enemyShips,  $attackPoints, $defensePoints);

            if ($winner === 'user') {
                // User wins
            } elseif ($winner === 'enemy') {
                // Enemy wins
            } else {
                // It's a draw
            }
        }
        $resourcesController = new RessourcesController();
        $resourcesController->transferResources($battle);
    }

    public function read(Request $request)
    {
        $user_id = Auth::user()->id;
        $showbattle = Battle::where('user_id', $user_id)->get();
        return response()->json($showbattle, 200);
    }
}
