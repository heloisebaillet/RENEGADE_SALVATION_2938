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
        $x1 = PlanetarySystem::select('x_coord')->where('user_id', $attacker_id)->get();
        $y1 = PlanetarySystem::select('y_coord')->where('user_id', $attacker_id)->get();

        $defender_id = $request->user_id;
        $x2 = PlanetarySystem::select('x_coord')->where('user_id', $defender_id)->get();
        $y2 = PlanetarySystem::select('y_coord')->where('user_id', $defender_id)->get();

        // $winner_id = $request->user_id;
        $resources_looted = Battle::where('user_id', $attacker_id)->get();
        $ttl_att_pts = "";
        $ttl_def_pts = "";

        $fuel = Ressources::select('quantity')->where('user_id', $attacker_id)->where('type', 'fuel')->get();

        $fighter = Ships::where('type', 'fighter')->get();
        $consoFighter = 1;
        $frigate = Ships::where('type', 'frigate')->get();
        $consoFrigate = 2;
        $cruiser = Ships::where('type', 'cruiser')->get();
        $consoCruiser = 4;
        $destroyer = Ships::where('type', 'destroyer')->get();
        $consoDestroyer = 8;


        // calcul de la distance entre le système planétaire de l'attaquant et du défenseur
        function calculateDistance($x1, $y1, $x2, $y2)
        {   // pow: c'est une fonction exponnentielle de calcul
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
            dump(die);
        }

        $fuelConsumed = ($fuel - $fuelNeeded);

        // mise à jour du fuel post travel
        $fuel->quantity = $fuelConsumed;
        $fuel->save();

        // on lance une boucle des rounds 
        function battleRounds($fighter, $frigate, $cruiser, $destroyer, $damage, &$shields, $attacker_id, $defender_id, $attackerDamage, $defenderDamage)
        {
            // $gameOver = Ships::where('quantity', '0')->get();

            $shipsAttacker = Ships::where('user_id', $attacker_id)->get();
            $shipsDefender = Ships::where('user_id', $defender_id)->get();
            $ships = Ships::where('quantity')->get();
            $attacker_id = Ships::where('attacker_id')->get();
            $defender_id = Ships::where('defender_id')->get();

            // on calcule les points d'attaque de tous les vaisseaux par type et par round
            if ($fighter && $shipsAttacker && $shipsDefender) {
                // 11 = points d'attaque déterminés à l'avance par nos soins
                $damage = ($fighter->quantity * (11 * rand(0.5, 1.5)));
            }
            if ($frigate && $shipsAttacker && $shipsDefender) {
                // 13 = points d'attaque déterminés à l'avance par nos soins
                $damage = ($frigate->quantity * (13 * rand(0.5, 1.5)));
            }
            if ($cruiser && $shipsAttacker && $shipsDefender) {
                // 14 = points d'attaque déterminés à l'avance par nos soins
                $damage = ($cruiser->quantity * (14 * rand(0.5, 1.5)));
            }
            if ($destroyer && $shipsAttacker && $shipsDefender) {
                // 27 = points d'attaque déterminés à l'avance par nos soins
                $damage = ($destroyer->quantity * (27 * rand(0.5, 1.5)));
            }

            // on calcule les points de défense de tous les vaisseaux par type et par round
            if ($fighter && $shipsAttacker && $shipsDefender) {
                // 7 = points de défense déterminés à l'avance par nos soins
                $shields = ($fighter->quantity * (7 * rand(0.5, 1.5)));
            }
            if ($frigate && $shipsAttacker && $shipsDefender) {
                // 5 = points de défense déterminés à l'avance par nos soins
                $shields = ($frigate->quantity * (5 * rand(0.5, 1.5)));
            }
            if ($cruiser && $shipsAttacker && $shipsDefender) {
                // 9 = points de défense déterminés à l'avance par nos soins
                $shields = ($cruiser->quantity * (9 * rand(0.5, 1.5)));
            }
            if ($destroyer && $shipsAttacker && $shipsDefender) {
                // 20 = points de défense déterminés à l'avance par nos soins
                $shields = ($destroyer->quantity * (20 * rand(0.5, 1.5)));
            }

            //Perte de 30% de ses vaisseaux au joueur perdant du round
            if ($fighter <= 0) {
                return $fighter->quantity = $ships * 0.7;
            }
            if ($fighter <= 0 && $frigate <= 0) {
                return $frigate->quantity = $ships * 0.7;
            }
            if ($fighter <= 0 && $frigate <= 0 && $cruiser <= 0) {
                return $cruiser->quantity = $ships * 0.7;
            }
            if ($fighter <= 0 && $frigate <= 0 && $cruiser <= 0 && $destroyer <= 0) {
                return $fighter->quantity = $ships * 0.7;
            }

            // on lance une boucle pour attaquer jusqu'à ce qu'une des deux
            // flottes ait la défense de tous ses vaisseaux à 0

            // $roundWinner = $damage < $shields;
            // $roundLoser = $shields < $damage;

            // foreach ($shipsAttacker as $shipsAtt) {

            //     $damage = $shipsAtt['attackPoints'];

            //     foreach ($shipsDefender as $shipsDef) {

            //         if ($damage <= 0) break;

            //         $shields = $shipsDef['defensePoints'];

            //         if ($damage >= $shields) {

            //             $roundDamageAttacker += $shields;
            //             $damage -= $shields;
            //             $shields = 0;
            //         } else {

            //             $roundDamageAttacker += $damage;
            //             $shields -= $damage;
            //             $damage = 0;
            //         }
            //     }
            // }

            // foreach ($shipsDefender as $shipsDef) {

            //     $damage = $shipsDef['attackPoints'];

            //     foreach ($shipsAttacker as $shipsAtt) {

            //         if ($damage <= 0) break;

            //         $shields = $shipsAtt['defensePoints'];

            //         if ($damage >= $shields) {

            //             $roundDamageDefender += $shields;
            //             $damage -= $shields;
            //             $shields = 0;
            //         } else {

            //             $roundDamageDefender += $damage;
            //             $shields -= $damage;
            //             $damage = 0;
            //         }
            //     }
            // }



            // $attackerDamage += $roundDamageAttacker;
            // $defenderDamage += $roundDamageDefender;

            // $attackerRemainingShips = array_filter($shipsAttacker, function ($ship) {
            //     return $ship['defensePoints'] > 0;
            // });
            // $defenderRemainingShips = array_filter($shipsDefender, function ($ship) {
            //     return $ship['defensePoints'] > 0;
            // });

            // if (empty($attackerRemainingShips) || empty($defenderRemainingShips)) {
            //     dump(die);
            // }


            // if ($attackerDamage > $defenderDamage) {
            //     return Response()->json('You win!');
            // } elseif ($attackerDamage < $defenderDamage) {
            //     return Response()->json('You lose!');
            // } else {
            //     return Response()->json("It's a draw!");
            // }

            // $winner = battleRounds($fighter, $frigate, $cruiser, $destroyer, $damage, $shields, $attacker_id, $defender_id, $attackerDamage, $defenderDamage);

            // if ($winner === 'attacker') {
            //     return Response()->json('You win!');
            // } elseif ($winner === 'defender') {
            //     return Response()->json('You lose!');
            // } else {
            //     return Response()->json("It's a draw!");
            // }

            // fin du combat quand une des flottes est à 0 vaisseaux
            if ($shipsAttacker <= 0 || $shipsDefender <= 0) {
                return 'game over';
            }
        }



        // on créé l'attaque si toutes les conditions recquises sont validées
        if ($fuelNeeded >= $fuel) {
            $battle = new Battle();
            $battle->attacker_id = $attacker_id;
            $battle->defender_id = $defender_id;
            $battle->ttl_att_pts = $ttl_att_pts;
            $battle->ttl_def_pts = $ttl_def_pts;
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
