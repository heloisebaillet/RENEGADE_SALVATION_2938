<?php

namespace App\Http\Controllers;

use App\Models\Battle;
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

    private function computeAttackRound($user_id, $defender_id, &$att, $battle_uuid)
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

    private function computeRound(
        $id,
        $looted_total,
        &$att,
        $isDefender,
        $battle_uuid
    ) {
        /*
        $att['nb_def_fighter'],
        $att['nb_def_frigate'],
        $att['nb_def_cruiser'],
        $att['nb_def_destroyer'],
        $att['nb_fighter'],
        $att['nb_frigate'],
        $att['nb_cruiser'],
        $att['nb_destroyer'],
        */
        $nb_fighters = $isDefender ? $att['nb_def_fighter'] : $att['nb_fighter'];
        $nb_frigates = $isDefender ? $att['nb_def_frigate'] : $att['nb_frigate'];
        $nb_cruisers = $isDefender ? $att['nb_def_cruiser'] : $att['nb_cruiser'];
        $nb_destroyers = $isDefender ? $att['nb_def_destroyer'] : $att['nb_destroyer'];

        $looted = $looted_total;
        $att_fighters = Ship::where('user_id', $id)->where('type', 'fighter')->first();
        if ($nb_fighters < $looted) {
            $att_fighters->quantity = $att_fighters->quantity - $nb_fighters < 0 ? 0 : $att_fighters->quantity - $nb_fighters;
            $att_fighters->save();
            $looted = $looted - $nb_fighters;
            $isDefender ? $att['nb_def_fighter'] = $att_fighters->quantity : $att['nb_fighter'] = $att_fighters->quantity;
            $this->recordBattle($battle_uuid, $id, "fighter", $looted, $att_fighters->quantity);
        } else {
            $att_fighters->quantity = $att_fighters->quantity - $looted < 0 ? 0 : $att_fighters->quantity - $looted;
            $isDefender ? $att['nb_def_fighter'] = $att_fighters->quantity : $att['nb_fighter'] = $att_fighters->quantity;
            $att_fighters->save();
            $this->recordBattle($battle_uuid, $id, "fighter", $looted, $att_fighters->quantity);
            return;
        }
        $att_frigates = Ship::where('user_id', $id)->where('type', 'frigate')->first();
        if ($nb_frigates < $looted) {
            $att_frigates->quantity = $att_frigates->quantity - $nb_frigates < 0 ? 0 : $att_frigates->quantity - $nb_frigates;
            $att_frigates->save();
            $looted = $looted - $nb_frigates;
            $isDefender ? $att['nb_def_frigate']  = $att_frigates->quantity : $att['nb_frigate'] = $att_frigates->quantity;
            $this->recordBattle($battle_uuid, $id, "frigate", $looted, $att_frigates->quantity);
        } else {
            $att_frigates->quantity = $att_frigates->quantity - $looted < 0 ? 0 : $att_frigates->quantity - $looted;
            $nb_frigates = $att_frigates->quantity;
            $isDefender ? $att['nb_def_frigate']  = $att_frigates->quantity : $att['nb_frigate'] = $att_frigates->quantity;
            $att_frigates->save();
            $this->recordBattle($battle_uuid, $id, "frigate", $looted, $att_frigates->quantity);
            return;
        }
        $att_cruisers = Ship::where('user_id', $id)->where('type', 'cruiser')->first();
        if ($nb_cruisers < $looted) {
            $att_cruisers->quantity = $att_cruisers->quantity - $nb_cruisers < 0 ? 0 : $att_cruisers->quantity - $nb_cruisers;
            $att_cruisers->save();
            $looted = $looted - $nb_cruisers;
            $isDefender ? $att['nb_def_cruiser'] = $att_cruisers->quantity : $att['nb_cruiser'] = $att_cruisers->quantity;
            $this->recordBattle($battle_uuid, $id, "cruisier", $looted, $att_cruisers->quantity);
        } else {
            $att_cruisers->quantity = $att_cruisers->quantity - $looted < 0 ? 0 : $att_cruisers->quantity - $looted;
            $isDefender ? $att['nb_def_cruiser'] = $att_cruisers->quantity : $att['nb_cruiser'] = $att_cruisers->quantity;
            $att_cruisers->save();
            $this->recordBattle($battle_uuid, $id, "cruisier", $looted, $att_cruisers->quantity);
            return;
        }
        $att_destroyers = Ship::where('user_id', $id)->where('type', 'destroyer')->first();
        if ($nb_destroyers < $looted) {
            $att_destroyers->quantity = $att_destroyers->quantity - $nb_destroyers < 0 ? 0 : $att_destroyers->quantity - $nb_destroyers;
            $att_destroyers->save();
            $looted = $looted - $nb_destroyers;
            $isDefender ? $att['nb_def_destroyer'] = $att_destroyers->quantity : $att['nb_destroyer'] = $att_destroyers->quantity;
            $this->recordBattle($battle_uuid, $id, "destroyer", $looted, $att_destroyers->quantity);
        } else {
            $att_destroyers->quantity = $att_destroyers->quantity - $looted < 0 ? 0 : $att_destroyers->quantity - $looted;
            $isDefender ? $att['nb_def_destroyer'] = $att_destroyers->quantity : $att['nb_destroyer'] = $att_destroyers->quantity;
            $att_destroyers->save();
            $this->recordBattle($battle_uuid, $id, "destroyer", $looted, $att_destroyers->quantity);
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
        $nb_att_ref = $att['nb_fighter'] + $att['nb_frigate'] + $att['nb_cruiser'] + $att['nb_destroyer'];

        $cursor_zero_att = Ship::where('user_id', $user_id)->sum('quantity') - $nb_att_ref;


        $nb_def = Ship::where('user_id', $request->defender_id)->sum('quantity');
        do {
            $this->computeAttackRound($user_id, $request->defender_id, $att, $battle_uuid);
            $nb_att = Ship::where('user_id', $user_id)->sum('quantity');
            $nb_def = Ship::where('user_id', $request->defender_id)->sum('quantity');
            Log::info('nb_att = ' . $nb_att . ' nb_def = ' . $nb_def);
        } while ($nb_att > $cursor_zero_att && $nb_def > 0);


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
