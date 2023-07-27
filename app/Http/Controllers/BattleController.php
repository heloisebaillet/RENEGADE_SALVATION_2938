<?php

namespace App\Http\Controllers;

use App\Models\Battle;
use App\Models\PlanetarySystem;

use App\Models\Ressources;
use App\Models\Ship;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;

class BattleController extends Controller
{


    public function getPlanetarySystems()
    {

        $user_id = Auth::id();

        $planetarySystems = PlanetarySystem::whereHas('user', function ($query) use ($user_id) {
            $query->where('id', '!=', $user_id);
        })->get();

        return response()->json(['status' => 'success', 'planetarySystems' => $planetarySystems]);
    }

    private function newUniqueId(): string
    {
        return (string) Uuid::uuid4();

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

        $fighter = Ship::where('type', 'fighter')->get();
        $consoFighter = 1;
        $frigate = Ship::where('type', 'frigate')->get();
        $consoFrigate = 2;
        $cruiser = Ship::where('type', 'cruiser')->get();
        $consoCruiser = 4;
        $destroyer = Ship::where('type', 'destroyer')->get();
        $consoDestroyer = 8;


        // calcul de la distance entre le système planétaire de l'attaquant et du défenseur
        function calculateDistance($x1, $y1, $x2, $y2)
        {   // pow: c'est une fonction exponnentielle de calcul
            // sqrt : fonction racine carré
            return sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));
        }


    private function computeAttackRound($user_id, $defender_id, &$att, $battle_uuid): bool
    {
        Log::info($att);
        $nb_att = $att['nb_fighter'] + $att['nb_frigate'] + $att['nb_cruiser'] + $att['nb_destroyer'];
        $nb_def = $att['nb_def_fighter'] + $att['nb_def_frigate'] + $att['nb_def_cruiser'] + $att['nb_def_destroyer'];
        // points attaques liés aux vaisseaux att et def
        $pt_att_fighter = (($att['nb_fighter'] * 5) * (mt_rand(500, 1500) / 1000));
        $pt_att_frigate = (($att['nb_frigate'] * 7) * (mt_rand(500, 1500) / 1000));
        $pt_att_cruiser = (($att['nb_cruiser'] * 9) * (mt_rand(500, 1500) / 1000));
        $pt_att_destroyer = (($att['nb_destroyer'] * 20) * (mt_rand(500, 1500) / 1000));
        $total_pt_att = ($pt_att_fighter + $pt_att_frigate + $pt_att_cruiser + $pt_att_destroyer);
        Log::info("total des points de l'attanquant = " . $total_pt_att);

        $pt_def_fighter = (($att['nb_def_fighter'] * 5) * (mt_rand(500, 1500) / 1000));
        $pt_def_frigate = (($att['nb_def_frigate'] * 7) * (mt_rand(500, 1500) / 1000));
        $pt_def_cruiser = (($att['nb_def_cruiser'] * 9) * (mt_rand(500, 1500) / 1000));
        $pt_def_destroyer = (($att['nb_def_destroyer'] * 20) * (mt_rand(500, 1500) / 1000));
        $total_pt_def = ($pt_def_fighter + $pt_def_frigate + $pt_def_cruiser  + $pt_def_destroyer);

        Log::info("total des points de la défense = " . $total_pt_def);

        // Attaquant gagnant
        if ($total_pt_att > $total_pt_def) {
            $this->computeRound(
                $defender_id,
                ceil($nb_def * 0.3),
                $att,
                true,
                $battle_uuid
            );
        }
        //Defendeur Gagnant
        else if ($total_pt_att < $total_pt_def) {
            $this->computeRound(
                $user_id,
                ceil($nb_att * 0.3),
                $att,
                false,
                $battle_uuid
            );
        }
        // Egalité
        else {
            $this->computeRound(
                $defender_id,
                ceil($nb_def * 0.3),
                $att,
                true,
                $battle_uuid
            );
            $this->computeRound(
                $user_id,
                ceil($nb_att * 0.3),
                $att,
                false,
                $battle_uuid
            );
        }
        if ($nb_att <= 0 || $nb_def <= 0) {
            // Envoi du signal que la bataille est terminée.
            return true;
        }

        // Si la bataille n'est pas terminée
        return false;
    }

    private function recordBattle($uuid, $id, $type, $pt_loose, $loose_ships)
    {
        $battle = new Battle();
        $battle->uuid = $uuid;
        $battle->type = $type;
        $battle->user_id = $id;
        $battle->pt_loose = $pt_loose;
        $battle->loose_ships = $loose_ships;
        $battle->save();
    }

    private function computeRound($id, $loose_total, &$att, $isDefender, $battle_uuid)
    {
        $nb_fighters = $isDefender ? $att['nb_def_fighter'] : $att['nb_fighter'];
        $nb_frigates = $isDefender ? $att['nb_def_frigate'] : $att['nb_frigate'];
        $nb_cruisers = $isDefender ? $att['nb_def_cruiser'] : $att['nb_cruiser'];
        $nb_destroyers = $isDefender ? $att['nb_def_destroyer'] : $att['nb_destroyer'];

        $loose_ships = $loose_total;

        $att_fighters = Ship::where('user_id', $id)->where('type', 'fighter')->first();
        if ($nb_fighters < $loose_ships && $nb_fighters != 0) {
            $att_fighters->quantity = $att_fighters->quantity - $nb_fighters;
            $att_fighters->save();
            $loose_ships = $loose_ships - $nb_fighters;
            $isDefender ? $att['nb_def_fighter'] = $att_fighters->quantity - $nb_fighters : $att['nb_fighter'] = $att_fighters->quantity - $nb_fighters;
            $this->recordBattle($battle_uuid, $id, "fighter", $loose_ships, $nb_fighters);
        } else {
            $att_fighters->quantity = max($att_fighters->quantity - $loose_ships, 0);
            $nb_fighters = $att_fighters->quantity;
            $isDefender ? $att['nb_def_fighter'] = $att_fighters->quantity : $att['nb_fighter'] = $att_fighters->quantity;
            $att_fighters->save();
            $this->recordBattle($battle_uuid, $id, "fighter", $loose_ships, $att_fighters->quantity);
            return;
        }


        $att_frigates = Ship::where('user_id', $id)->where('type', 'frigate')->first();
        
        if ($nb_frigates < $loose_ships && $nb_frigates != 0) {
            $att_frigates->quantity = $att_frigates->quantity - $nb_frigates;
            $att_frigates->save();
            $loose_ships = $loose_ships - $nb_frigates;
            $isDefender ? $att['nb_def_frigate'] = $att_frigates->quantity - $nb_frigates : $att['nb_frigate'] = $att_frigates->quantity - $nb_frigates;
            $this->recordBattle($battle_uuid, $id, "frigate", $loose_ships, $att_frigates->quantity);
        } else {
            $att_frigates->quantity = max($att_frigates->quantity - $loose_ships, 0);
            $nb_frigates = $att_frigates->quantity;
            $isDefender ? $att['nb_def_frigate']  = $att_frigates->quantity : $att['nb_frigate'] = $att_frigates->quantity;
            $att_frigates->save();
            $this->recordBattle($battle_uuid, $id, "frigate", $loose_ships, $att_frigates->quantity);
            return;
        }

            $shipsAttacker = Ship::where('user_id', $attacker_id)->get();
            $shipsDefender = Ship::where('user_id', $defender_id)->get();
            $ships = Ship::where('quantity')->get();
            $attacker_id = Ship::where('attacker_id')->get();
            $defender_id = Ship::where('defender_id')->get();


        $att_cruisers = Ship::where('user_id', $id)->where('type', 'cruiser')->first();
        if ($nb_cruisers < $loose_ships && $nb_cruisers != 0) {
            $att_cruisers->quantity = $att_cruisers->quantity - $nb_cruisers;
            $att_cruisers->save();
            $loose_ships = $loose_ships - $nb_cruisers;
            $isDefender ? $att['nb_def_cruiser'] = $att_cruisers->quantity - $nb_cruisers : $att['nb_cruiser'] = $att_cruisers->quantity - $nb_cruisers;
            $this->recordBattle($battle_uuid, $id, "cruiser", $loose_ships, $att_cruisers->quantity);
        } else {
            $att_cruisers->quantity = max($att_cruisers->quantity - $loose_ships, 0);
            $nb_cruisers = $att_cruisers->quantity;
            $isDefender ? $att['nb_def_cruiser']  = $att_cruisers->quantity : $att['nb_cruiser'] = $att_cruisers->quantity;
            $att_cruisers->save();
            $this->recordBattle($battle_uuid, $id, "cruiser", $loose_ships, $att_cruisers->quantity);
            return;
        }

        $att_destroyers = Ship::where('user_id', $id)->where('type', 'destroyer')->first();
        if ($nb_destroyers < $loose_ships && $nb_destroyers != 0) {
            $att_destroyers->quantity = $att_destroyers->quantity - $nb_destroyers;
            $att_destroyers->save();
            $loose_ships = $loose_ships - $nb_destroyers;
            $isDefender ? $att['nb_def_destroyer'] = $att_destroyers->quantity - $nb_destroyers : $att['nb_destroyer'] = $att_destroyers->quantity - $nb_destroyers;
            $this->recordBattle($battle_uuid, $id, "destroyer", $loose_ships, $att_destroyers->quantity);
        } else {
            $att_destroyers->quantity = max($att_destroyers->quantity - $loose_ships, 0);
            $nb_destroyers = $att_destroyers->quantity;
            $isDefender ? $att['nb_def_cruiser']  = $att_destroyers->quantity : $att['nb_cruiser'] = $att_destroyers->quantity;
            $att_destroyers->save();
            $this->recordBattle($battle_uuid, $id, "cruiser", $loose_ships, $att_destroyers->quantity);
            return;
        }
    }

    public function attack(Request $request)
    {
        $request->validate([
            'defender_id' => 'required|int',
            'nb_fighter' => 'required|int',
            'nb_frigate' => 'required|int',
            'nb_cruiser' => 'required|int',
            'nb_destroyer' => 'required|int',
            'fuel_needed' => 'required|int',
        ]);
        //données de l'attaquant (nous)
        $user_id =  Auth::id();
        $battle_uuid = $this->newUniqueId();
        // calcule système de combat
        $att['nb_fighter'] = intval($request->nb_fighter);
        $att['nb_frigate'] = intval($request->nb_frigate);
        $att['nb_cruiser'] = intval($request->nb_cruiser);
        $att['nb_destroyer'] = intval($request->nb_destroyer);

        $nb_def_fighters = Ship::where('user_id', $request->defender_id)->where('type', 'fighter')->first();
        $nb_def_frigates = Ship::where('user_id', $request->defender_id)->where('type', 'frigate')->first();
        $nb_def_cruisers = Ship::where('user_id', $request->defender_id)->where('type', 'cruiser')->first();
        $nb_def_destroyers = Ship::where('user_id', $request->defender_id)->where('type', 'destroyer')->first();

        $att['nb_def_fighter'] = intval($nb_def_fighters->quantity);
        $att['nb_def_frigate'] = intval($nb_def_frigates->quantity);
        $att['nb_def_cruiser'] = intval($nb_def_cruisers->quantity);
        $att['nb_def_destroyer'] = intval($nb_def_destroyers->quantity);

        // nombre engagé dans la bataille
        $nb_att = $att['nb_fighter'] + $att['nb_frigate'] + $att['nb_cruiser'] + $att['nb_destroyer'];
        $nb_def = $att['nb_def_fighter'] + $att['nb_def_frigate'] + $att['nb_def_cruiser'] + $att['nb_def_destroyer'];
        $total_ships_user_id = Ship::where('user_id', $user_id)->sum('quantity');


        // Boucle de bataille
        $nb_att = $att['nb_fighter'] + $att['nb_frigate'] + $att['nb_cruiser'] + $att['nb_destroyer'];
        $nb_def = $att['nb_def_fighter'] + $att['nb_def_frigate'] + $att['nb_def_cruiser'] + $att['nb_def_destroyer'];

        $rounds = 0;
        while ($nb_att > 0 && $nb_def > 0) {
            $battleEnded = $this->computeAttackRound($user_id, $request->defender_id, $att, $battle_uuid);
            $nb_att = $att['nb_fighter'] + $att['nb_frigate'] + $att['nb_cruiser'] + $att['nb_destroyer'];
            $nb_def = $att['nb_def_fighter'] + $att['nb_def_frigate'] + $att['nb_def_cruiser'] + $att['nb_def_destroyer'];

            Log::info('nb_att = ' . $nb_att . ' nb_def = ' . $nb_def);

            // Vérifier si la bataille est terminée
            if ($battleEnded || $rounds >= 100) { // Add a maximum number of rounds to prevent infinite loops
                break;
            }

            $rounds++;

            //Perte de 30% de ses vaisseaux au joueur perdant du round
            if ($fighter <= 0) {
                return $fighter->quantity = $ships * 0.3;
            }
            if ($fighter <= 0 && $frigate <= 0) {
                return $frigate->quantity = $ships * 0.3;
            }
            if ($fighter <= 0 && $frigate <= 0 && $cruiser <= 0) {
                return $cruiser->quantity = $ships * 0.3;
            }
            if ($fighter <= 0 && $frigate <= 0 && $cruiser <= 0 && $destroyer <= 0) {
                return $fighter->quantity = $ships * 0.3;
            }

            // mise à jour du nombre de vaisseaux post battle

            $ships->quantity->save();

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


        return response()->json([
            'defender_id' => $request->defender_id,
            'user_id' => $user_id,
            'attack_ship_count' => $nb_att,
            'defender_ship_count' => $nb_def,
        ]);


        // // Round  1 Fighter
        // if ($pt_att_fighter > $pt_def_fighter) {
        //     $reductionfighter = $nb_def_fighters->quantity * 0.3;
        //     $newQuantity = $nb_def_fighters->quantity - $reductionfighter;
        //     $newQuantity = ceil($newQuantity);
        //     $nb_def_fighters->quantity = $newQuantity;
        //     $nb_def_fighters->save();
        //     // Round  2 Fighter
        //     if ($pt_att_fighter > $pt_def_frigate) {
        //         $reductionfrigate = $nb_def_frigates->quantity * 0.3;
        //         $newQuantity = $nb_def_frigates->quantity - $reductionfrigate;
        //         $newQuantity = ceil($newQuantity);
        //         $nb_def_frigates->quantity = $newQuantity;
        //         $nb_def_frigates->save();
        //         // Round  3 Fighter
        //         if ($pt_att_fighter > $pt_def_cruiser) {
        //             $reductioncruiser = $nb_def_cruisers->quantity * 0.3;
        //             $newQuantity = $nb_def_cruisers->quantity - $reductioncruiser;
        //             $newQuantity = ceil($newQuantity);
        //             $nb_def_cruisers->quantity = $newQuantity;
        //             $nb_def_cruisers->save();

        //             // Round 4 Fighter
        //             if ($pt_att_fighter > $pt_def_destroyer) {
        //                 $reductiondestroyer = $nb_def_destroyers->quantity * 0.3;
        //                 $newQuantity = $nb_def_destroyers->quantity - $reductiondestroyer;
        //                 $newQuantity = ceil($newQuantity);
        //                 $nb_def_destroyers->quantity = $newQuantity;
        //                 $nb_def_destroyers->save();
        //                 $win_fighter = "attacker win";
        //             } else {
        //                 $win_fighter  = "round 4";
        //                 $reductionfighter = $nb_att_fighters * 0.3;
        //                 $newQuantity = $nb_att_fighters - $reductionfighter;
        //                 $newQuantity = ceil($newQuantity);
        //                 $nb_att_fighters = $newQuantity;
        //                 $att_fighters->quantity = $nb_att_fighters;
        //                 $att_fighters->save();
        //             }
        //         } else {
        //             $win_fighter  = "round 3";
        //             $reductionfighter = $nb_att_fighters * 0.3;
        //             $newQuantity = $nb_att_fighters - $reductionfighter;
        //             $newQuantity = ceil($newQuantity);
        //             $nb_att_fighters = $newQuantity;
        //             $att_fighters->quantity = $nb_att_fighters;
        //             $att_fighters->save();
        //         }
        //     } else {
        //         $win_fighter  = "round 2";
        //         $reductionfighter = $nb_att_fighters * 0.3;
        //         $newQuantity = $nb_att_fighters - $reductionfighter;
        //         $newQuantity = ceil($newQuantity);
        //         $nb_att_fighters = $newQuantity;
        //         $att_fighters->quantity = $nb_att_fighters;
        //         $att_fighters->save();
        //     }
        // } else {
        //     $win_fighter  = "round 1";
        //     $reductionfighter = $nb_att_fighters * 0.3;
        //     $newQuantity = $nb_att_fighters - $reductionfighter;
        //     $newQuantity = ceil($newQuantity);
        //     $nb_att_fighters = $newQuantity;
        //     $att_fighters->quantity = $nb_att_fighters;
        //     $att_fighters->save();
        // }
        // // Round  1 Frigate
        // if ($pt_att_frigate > $pt_def_fighter) {
        //     $reductionfighter = $nb_def_fighters->quantity * 0.3;
        //     $newQuantity = $nb_def_fighters->quantity - $reductionfighter;
        //     $newQuantity = ceil($newQuantity);
        //     $nb_def_fighters->quantity = $newQuantity;
        //     $nb_def_fighters->save();
        //     // Round  2 Frigate
        //     if ($pt_att_frigate > $pt_def_frigate) {
        //         $reductionfrigate = $nb_def_frigates->quantity * 0.3;
        //         $newQuantity = $nb_def_frigates->quantity - $reductionfrigate;
        //         $newQuantity = ceil($newQuantity);
        //         $nb_def_frigates->quantity = $newQuantity;
        //         $nb_def_frigates->save();

        //         // Round  3 Frigate
        //         if ($pt_att_frigate > $pt_def_cruiser) {
        //             $reductioncruiser = $nb_def_cruisers->quantity * 0.3;
        //             $newQuantity = $nb_def_cruisers->quantity - $reductioncruiser;
        //             $newQuantity = ceil($newQuantity);
        //             $nb_def_cruisers->quantity = $newQuantity;
        //             $nb_def_cruisers->save();

        //             // Round 4 Frigate
        //             if ($pt_att_frigate > $pt_def_destroyer) {
        //                 $reductiondestroyer = $nb_def_destroyers->quantity * 0.3;
        //                 $newQuantity = $nb_def_destroyers->quantity - $reductiondestroyer;
        //                 $newQuantity = ceil($newQuantity);
        //                 $nb_def_destroyers->quantity = $newQuantity;
        //                 $nb_def_destroyers->save();
        //                 $win_frigate = "attacker win";
        //             } else {
        //                 $win_frigate  = "round 4";
        //                 $reductionfrigate = $nb_att_frigates * 0.3;
        //                 $newQuantity = $nb_att_frigates - $reductionfrigate;
        //                 $newQuantity = ceil($newQuantity);
        //                 $nb_att_frigates = $newQuantity;
        //                 $att_frigates->quantity = $nb_att_frigates;
        //                 $att_frigates->save();
        //             }
        //         } else {
        //             $win_frigate  = "round 3";
        //             $reductionfrigate = $nb_att_frigates * 0.3;
        //             $newQuantity = $nb_att_frigates - $reductionfrigate;
        //             $newQuantity = ceil($newQuantity);
        //             $nb_att_frigates = $newQuantity;
        //             $att_frigates->quantity = $nb_att_frigates;
        //             $att_frigates->save();
        //         }
        //     } else {
        //         $win_frigate  = " round 2";
        //         $reductionfrigate = $nb_att_frigates * 0.3;
        //         $newQuantity = $nb_att_frigates - $reductionfrigate;
        //         $newQuantity = ceil($newQuantity);
        //         $nb_att_frigates = $newQuantity;
        //         $att_frigates->quantity = $nb_att_frigates;
        //         $att_frigates->save();
        //     }
        // } else {
        //     $win_frigate  = "round 1";
        //     $reductionfrigate = $nb_att_frigates * 0.3;
        //     $newQuantity = $nb_att_frigates - $reductionfrigate;
        //     $newQuantity = ceil($newQuantity);
        //     $nb_att_frigates = $newQuantity;
        //     $att_frigates->quantity = $nb_att_frigates;
        //     $att_frigates->save();
        // }
        // // Round  1 Cruiser
        // if ($pt_att_cruiser > $pt_def_fighter) {
        //     $reductionfighter = $nb_def_fighters->quantity * 0.3;
        //     $newQuantity = $nb_def_fighters->quantity - $reductionfighter;
        //     $newQuantity = ceil($newQuantity);
        //     $nb_def_fighters->quantity = $newQuantity;
        //     $nb_def_fighters->save();

        //     // Round  2 Cruiser
        //     if ($pt_att_cruiser > $pt_def_frigate) {
        //         $reductionfrigate = $nb_def_frigates->quantity * 0.3;
        //         $newQuantity = $nb_def_frigates->quantity - $reductionfrigate;
        //         $newQuantity = ceil($newQuantity);
        //         $nb_def_frigates->quantity = $newQuantity;
        //         $nb_def_frigates->save();

        //         // Round  3 Cruiser
        //         if ($pt_att_cruiser > $pt_def_cruiser) {
        //             $reductioncruiser = $nb_def_cruisers->quantity * 0.3;
        //             $newQuantity = $nb_def_cruisers->quantity - $reductioncruiser;
        //             $newQuantity = ceil($newQuantity);
        //             $nb_def_cruisers->quantity = $newQuantity;
        //             $nb_def_cruisers->save();


        //             // Round 4 Cruiser
        //             if ($pt_att_cruiser > $pt_def_destroyer) {
        //                 $reductiondestroyer = $nb_def_destroyers->quantity * 0.3;
        //                 $newQuantity = $nb_def_destroyers->quantity - $reductiondestroyer;
        //                 $newQuantity = ceil($newQuantity);
        //                 $nb_def_destroyers->quantity = $newQuantity;
        //                 $nb_def_destroyers->save();
        //                 $win_cruiser = "attacker win";
        //             } else {
        //                 $win_cruiser  = " round 4";
        //                 $reductioncruiser = $nb_att_cruisers * 0.3;
        //                 $newQuantity = $nb_att_cruisers - $reductioncruiser;
        //                 $newQuantity = ceil($newQuantity);
        //                 $nb_att_cruisers = $newQuantity;
        //                 $att_cruisers->quantity = $nb_att_cruisers;
        //                 $att_cruisers->save();
        //             }
        //         } else {
        //             $win_cruiser  = "round 3";
        //             $reductioncruiser = $nb_att_cruisers * 0.3;
        //             $newQuantity = $nb_att_cruisers - $reductioncruiser;
        //             $newQuantity = ceil($newQuantity);
        //             $nb_att_cruisers = $newQuantity;
        //             $att_cruisers->quantity = $nb_att_cruisers;
        //             $att_cruisers->save();
        //         }
        //     } else {
        //         $win_cruiser  = "round 2";
        //         $reductioncruiser = $nb_att_cruisers * 0.3;
        //         $newQuantity = $nb_att_cruisers - $reductioncruiser;
        //         $newQuantity = ceil($newQuantity);
        //         $nb_att_cruisers = $newQuantity;
        //         $att_cruisers->quantity = $nb_att_cruisers;
        //         $att_cruisers->save();
        //     }
        // } else {
        //     $win_cruiser  = "round 1";
        //     $reductioncruiser = $nb_att_cruisers * 0.3;
        //     $newQuantity = $nb_att_cruisers - $reductioncruiser;
        //     $newQuantity = ceil($newQuantity);
        //     $nb_att_cruisers = $newQuantity;
        //     $att_cruisers->quantity = $nb_att_cruisers;
        //     $att_cruisers->save();
        // }
        // // Round  1 Destroyer
        // if ($pt_att_destroyer > $pt_def_fighter) {
        //     $reductionfighter = $nb_def_fighters->quantity * 0.3;
        //     $newQuantity = $nb_def_fighters->quantity - $reductionfighter;
        //     $newQuantity = ceil($newQuantity);
        //     $nb_def_fighters->quantity = $newQuantity;
        //     $nb_def_fighters->save();

        //     // Round  2 Destroyer
        //     if ($pt_att_destroyer > $pt_def_frigate) {
        //         $reductionfrigate = $nb_def_frigates->quantity * 0.3;
        //         $newQuantity = $nb_def_frigates->quantity - $reductionfrigate;
        //         $newQuantity = ceil($newQuantity);
        //         $nb_def_frigates->quantity = $newQuantity;
        //         $nb_def_frigates->save();

        //         // Round  3 Destroyer
        //         if ($pt_att_destroyer > $pt_def_cruiser) {
        //             $reductioncruiser = $nb_def_cruisers->quantity * 0.3;
        //             $newQuantity = $nb_def_cruisers->quantity - $reductioncruiser;
        //             $newQuantity = ceil($newQuantity);
        //             $nb_def_cruisers->quantity = $newQuantity;
        //             $nb_def_cruisers->save();

        //             // Round 4 Destroyer
        //             if ($pt_att_destroyer > $pt_def_destroyer) {
        //                 $reductiondestroyer = $nb_def_destroyers->quantity * 0.3;
        //                 $newQuantity = $nb_def_destroyers->quantity - $reductiondestroyer;
        //                 $newQuantity = ceil($newQuantity);
        //                 $nb_def_destroyers->quantity = $newQuantity;
        //                 $nb_def_cruisers->save();
        //                 $win_destroyer = "attacker win";
        //             } else {
        //                 $win_destroyer  = "round 4";
        //                 $reductiondestroyer = $nb_att_destroyers * 0.3;
        //                 $newQuantity = $nb_att_destroyers - $reductiondestroyer;
        //                 $newQuantity = ceil($newQuantity);
        //                 $nb_att_destroyers = $newQuantity;
        //                 $att_destroyers->quantity = $nb_att_destroyers;
        //                 $att_destroyers->save();
        //             }
        //         } else {
        //             $win_destroyer  = "round 3";
        //             $reductiondestroyer = $nb_att_destroyers * 0.3;
        //             $newQuantity = $nb_att_destroyers - $reductiondestroyer;
        //             $newQuantity = ceil($newQuantity);
        //             $nb_att_destroyers = $newQuantity;
        //             $att_destroyers->quantity = $nb_att_destroyers;
        //             $att_destroyers->save();
        //         }
        //     } else {
        //         $win_destroyer  = "round 2";
        //         $reductiondestroyer = $nb_att_destroyers * 0.3;
        //         $newQuantity = $nb_att_destroyers - $reductiondestroyer;
        //         $newQuantity = ceil($newQuantity);
        //         $nb_att_destroyers = $newQuantity;
        //         $att_destroyers->quantity = $nb_att_destroyers;
        //         $att_destroyers->save();
        //     }
        // } else {
        //     $win_destroyer  = "round 1";
        //     $reductiondestroyer = $nb_att_destroyers * 0.3;
        //     $newQuantity = $nb_att_destroyers - $reductiondestroyer;
        //     $newQuantity = ceil($newQuantity);
        //     $nb_att_destroyers = $newQuantity;
        //     $att_destroyers->quantity = $nb_att_destroyers;
        //     $att_destroyers->save();
        // }

        // mise à jour des pertes des vaisseaux

    }
}
