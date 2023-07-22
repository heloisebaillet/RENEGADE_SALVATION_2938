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
        $attacker_id = Auth::user()->id;
        $x1 = PlanetarySystem::where('user_id', $attacker_id)->x_coord;
        $y1 = PlanetarySystem::where('user_id', $attacker_id)->y_coord;
        $defender_id = $request->user_id;
        $x2 = PlanetarySystem::where('user_id', $defender_id)->x_coord;
        $y2 = PlanetarySystem::where('user_id', $defender_id)->y_coord;
        $resources_looted = Battle::class()->user_id;
        $fuel = Ressources::select('quantity')->where('user_id', $user_id)->where('type', 'fuel')->get();
        $fighter = Ships::class()->type('fighter');
        $consoFighter = 1;
        $frigate = Ships::class()->type('frigate');
        $consoFrigate = 2;
        $cruiser = Ships::class()->type('cruiser');
        $consoCruiser = 4;
        $destroyer = Ships::class()->type('destroyer');
        $consoDestroyer = 8;

        // calcul de la distance entre le système planétaire de l'attaquant et du défenseur
        function calculateDistance($x1, $y1, $x2, $y2)
        { // pow: c'est une fonction exponnentielle de calcul
            // sqrt : fonction racine carré
            return sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));
        }

        if ($fighter) {
            $fuelNeeded = ((calculateDistance($x1, $y1, $x2, $y2) * $consoFighter) / 10);
        }
        if ($frigate) {
            $fuelNeeded = ((calculateDistance($x1, $y1, $x2, $y2) * $consoFrigate) / 10);
        }
        if ($cruiser) {
            $fuelNeeded = ((calculateDistance($x1, $y1, $x2, $y2) * $consoCruiser) / 10);
        }
        if ($destroyer) {
            $fuelNeeded = ((calculateDistance($x1, $y1, $x2, $y2) * $consoDestroyer) / 10);
        } else {
            return Response()->json(['success' => 'false'], 400);
        }

        $fuelConsumed = ($fuel - $fuelNeeded);

        // mise à jour du fuel post travel
        $fuel->quantity =  $fuel->quantity - $fuelConsumed;
        $fuel->save();

        // on lance une boucle des rounds pour déterminer les points d'attaques qui infligent 
        // des dégâts sur les points de défense des flottes 
        function battleRounds($battleShips, $attackPoints, $defensePoints, $damage)
        {
            // on indique les points d'attaque et de défense des vaisseaux
            $battleShips = [
                ['type' => 'fighter'],
                ['type' => 'frigate'],
                ['type' => 'cruiser'],
                ['type' => 'destroyer']
            ];

            $attackPoints = [
                ['attackPoints' => 7],
                ['attackPoints' => 13],
                ['attackPoints' => 14],
                ['attackPoints' => 27]
            ];

            $defensePoints = [
                ['defensePoints' => 11],
                ['defensePoints' => 5],
                ['defensePoints' => 9],
                ['defensePoints' => 20]
            ];

            //$damage = ;
            // Pour chaque type vaisseau attaquant, on calcule le nombre de points d'attaque en faisant :
            //nombre de vaisseaux x points d'attaque du vaisseau x facteur de chance (aléatoire entre 0.5 et 1.5)
            //$defense =;
            //Pour chaque type vaisseau défenseur, on calcule le nombre de points de défense en faisant :
            //nombre de vaisseaux x points de défense du vaisseau x facteur de chance (aléatoire entre 0.5 et 1.5)

            while (true) {
                // on lance une boucle pour attaquer jusqu'à ce qu'une des deux
                // flottes ait la défense de tous ses vaisseaux à 0
                $roundDamageAttacker = '';
                $roundDamageDefender = '';

                foreach ($battleShips as &$ship) {

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

        // on créé l'attaque si toutes les conditions recquises sont validées
        if ($fuelNeeded >= $fuel) {
            $battle = new Battle();
            $battle->attacker_id = $attacker_id;
            $battle->defender_id = $defender_id;
            $battle->winner_id = $winner_id;
            $battle->resources_looted = $resources_looted;
            $battle->save();
            $resourcesController = new RessourcesController();
            $resourcesController->transferResources($battle);
        }
    }

    public function read()
    {
        $user_id = Auth::user()->id;
        $showbattle = Battle::where('user_id', $user_id)->get();
        return response()->json($showbattle, 200);
    }
}
