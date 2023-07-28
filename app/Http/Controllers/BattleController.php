<?php

namespace App\Http\Controllers;

use App\Models\Battle;
use App\Models\Round;
use App\Models\PlanetarySystem;
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
    }

    private function computeAttackRound($user_id, $defender_id, $battle_uuid)
    {
        $nb_att = $this->getNb($battle_uuid, $user_id);
        $nb_def = $this->getNb($battle_uuid, $defender_id);
        $attaker = Round::where('uuid', $battle_uuid)->where('user_id', $user_id)->first();
        $defender = Round::where('uuid', $battle_uuid)->where('user_id', $defender_id)->first();

        // points attaques liés aux vaisseaux att et def
        $rnd_att = (mt_rand(500, 1500) / 1000);
        $pt_att_fighter = (($attaker->nb_fighter * 5) * $rnd_att);
        $pt_att_frigate = (($attaker->nb_frigate * 7) * $rnd_att);
        $pt_att_cruiser = (($attaker->nb_cruiser * 9) * $rnd_att);
        $pt_att_destroyer = (($attaker->nb_destroyer * 20) * $rnd_att);
        $total_pt_att = ($pt_att_fighter + $pt_att_frigate + $pt_att_cruiser + $pt_att_destroyer);
        Log::info("total des points de l'attanquant = " . $total_pt_att);

        $rnd_def = (mt_rand(500, 1500) / 1000);
        $pt_def_fighter = (($defender->nb_fighter * 5) * $rnd_def);
        $pt_def_frigate = (($defender->nb_frigate * 7) * $rnd_def);
        $pt_def_cruiser = (($defender->nb_cruiser * 9) * $rnd_def);
        $pt_def_destroyer = (($defender->nb_destroyer * 20) * $rnd_def);
        $total_pt_def = ($pt_def_fighter + $pt_def_frigate + $pt_def_cruiser  + $pt_def_destroyer);

        Log::info("total des points de la défense = " . $total_pt_def);

        // Attaquant gagnant
        if ($total_pt_att > $total_pt_def) {
            // Attaquant remporte le round
            $this->computeRound(
                $defender,
                ceil($nb_def * 0.3),
                true
            );
        }
        //Defendeur Gagnant
        else if ($total_pt_att < $total_pt_def) {
            // Defendeur remporte le round
            $this->computeRound(
                $attaker,
                ceil($nb_att * 0.3),
                false
            );
        }
        // Egalité
        else {
            $this->computeRound(
                $defender,
                ceil($nb_def * 0.3),
                true
            );
            $this->computeRound(
                $attaker,
                ceil($nb_att * 0.3),
                false
            );
        }
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

    private function computeRound($model, $loose_total, $isDefender)
    {
        $nb_fighters = $model->nb_fighter;
        $nb_frigates = $model->nb_frigate;
        $nb_cruisers = $model->nb_cruiser;
        $nb_destroyers = $model->nb_destroyer;

        $loose_ships = $loose_total;

        if ($nb_fighters < $loose_ships) {
            $model->nb_fighter = 0;
            $model->save();
            $loose_ships = $loose_ships - $nb_fighters;
            $this->recordBattle($model->uuid, $model->user_id, "fighter", $loose_ships, $model->nb_fighter);
        } else {
            $model->nb_fighter = $model->nb_fighter - $loose_ships;
            $model->save();
            $this->recordBattle($model->uuid, $model->user_id, "fighter", $loose_ships, $model->nb_fighter);
            return;
        }

        if ($nb_frigates < $loose_ships) {
            $model->nb_frigate = 0;
            $model->save();
            $loose_ships = $loose_ships - $nb_frigates;
            $this->recordBattle($model->uuid, $model->user_id, "frigate", $loose_ships, $model->nb_frigate);
        } else {
            $model->nb_frigate = $model->nb_frigate - $loose_ships;
            $model->save();
            $this->recordBattle($model->uuid, $model->user_id, "frigate", $loose_ships, $model->nb_frigate);
            return;
        }

        if ($nb_cruisers < $loose_ships) {
            $model->nb_cruiser = 0;
            $model->save();
            $loose_ships = $loose_ships - $nb_cruisers;
            $this->recordBattle($model->uuid, $model->user_id, "cruiser", $loose_ships, $model->nb_cruiser);
        } else {
            $model->nb_cruiser = $model->nb_cruiser - $loose_ships;
            $model->save();
            $this->recordBattle($model->uuid, $model->user_id, "cruiser", $loose_ships, $model->nb_cruiser);
            return;
        }

        if ($nb_destroyers < $loose_ships) {
            $model->nb_destroyer = 0;
            $model->save();
            $loose_ships = $loose_ships - $nb_destroyers;
            $this->recordBattle($model->uuid, $model->user_id, "destroyer", $loose_ships, $model->nb_destroyer);
        } else {
            $model->nb_destroyer = $model->nb_destroyer - $loose_ships;
            $model->save();
            $this->recordBattle($model->uuid, $model->user_id, "destroyer", $loose_ships, $model->nb_destroyer);
            return;
        }
    }

    private function getNb($battleUuid, $id)
    {
        $round = Round::where('uuid', $battleUuid)->where('user_id', $id)->first();
        return $round->nb_fighter + $round->nb_frigate + $round->nb_cruiser + $round->nb_destroyer;
    }

    private function updateResults($user_id, $roundValue, $type) {
        $ship = Ship::where('user_id', $user_id)
        ->where('type', $type)
        ->first();
        $ship->quantity =  $ship->quantity -  $roundValue;
        $ship->save();
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

        $nb_def_fighters = Ship::where('user_id', $request->defender_id)->where('type', 'fighter')->first();
        $nb_def_frigates = Ship::where('user_id', $request->defender_id)->where('type', 'frigate')->first();
        $nb_def_cruisers = Ship::where('user_id', $request->defender_id)->where('type', 'cruiser')->first();
        $nb_def_destroyers = Ship::where('user_id', $request->defender_id)->where('type', 'destroyer')->first();

        // nombre engagé dans la bataille
        $nb_att = $request->nb_fighter + $request->nb_frigate + $request->nb_cruiser + $request->nb_destroyer;
        $nb_def = $nb_def_fighters->quantity + $nb_def_frigates->quantity + $nb_def_cruisers->quantity + $nb_def_destroyers->quantity;

        Round::create([
            'uuid' => $battle_uuid,
            'user_id' => $user_id,
            'is_defender' => false,
            'nb_fighter' => intval($request->nb_fighter),
            'nb_frigate' => intval($request->nb_frigate),
            'nb_cruiser' => intval($request->nb_cruiser),
            'nb_destroyer' => intval($request->nb_destroyer)
        ]);

        Round::create([
            'uuid' => $battle_uuid,
            'user_id' => $request->defender_id,
            'is_defender' => true,
            'nb_fighter' => intval($nb_def_fighters->quantity),
            'nb_frigate' => intval($nb_def_frigates->quantity),
            'nb_cruiser' => intval($nb_def_cruisers->quantity),
            'nb_destroyer' => intval($nb_def_destroyers->quantity)
        ]);

        while ($nb_att > 0 && $nb_def > 0) {
            $this->computeAttackRound($user_id, $request->defender_id, $battle_uuid);
            $nb_att = $this->getNb($battle_uuid, $user_id);
            $nb_def = $this->getNb($battle_uuid, $request->defender_id);

            Log::info('nb_att = ' . $nb_att . ' nb_def = ' . $nb_def);
        }
        // Mise a jour table de données
        $round = Round::where('uuid', $battle_uuid)->where('user_id', $user_id)->first();
        $this->updateResults($user_id, $request->nb_fighter - $round->nb_fighter, 'fighter');
        $this->updateResults($user_id, $request->nb_frigate - $round->nb_frigate, 'frigate');
        $this->updateResults($user_id, $request->nb_cruiser - $round->nb_cruiser, 'cruiser');
        $this->updateResults($user_id, $request->nb_destroyer - $round->nb_destroyer, 'destroyer');

        $round = Round::where('uuid', $battle_uuid)->where('user_id', $request->defender_id)->first();
        $this->updateResults($request->defender_id, $nb_def_fighters->quantity - $round->nb_fighter, 'fighter');
        $this->updateResults($request->defender_id, $nb_def_frigates->quantity - $round->nb_frigate, 'frigate');
        $this->updateResults($request->defender_id, $nb_def_cruisers->quantity - $round->nb_cruiser, 'cruiser');
        $this->updateResults($request->defender_id, $nb_def_destroyers->quantity - $round->nb_destroyer, 'destroyer');


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
