<?php

namespace App\Http\Controllers;

use App\Models\PlanetarySystem;
use App\Models\Ship;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
        $nb_att_fighters = $request->nb_fighter;
        $nb_att_frigates = $request->nb_frigate;
        $nb_att_cruisers = $request->nb_cruiser;
        $nb_att_destroyers = $request->nb_destroyer;
        $fuel_att_needed = $request->fuel_needed;
        $att_fighters = Ship::where('user_id', $user_id)->where('type', 'fighter')->first();
        $att_frigates = Ship::where('user_id', $user_id)->where('type', 'frigate')->first();
        $att_cruisers = Ship::where('user_id', $user_id)->where('type', 'cruiser')->first();
        $att_destroyers = Ship::where('user_id', $user_id)->where('type', 'destroyer')->first();


        // données du défenseur (ennemie)
        $defender_id = $request->defender_id;
        $nb_def_fighters = Ship::where('user_id', $defender_id)->where('type', 'fighter')->first();
        $nb_def_frigates = Ship::where('user_id', $defender_id)->where('type', 'frigate')->first();
        $nb_def_cruisers = Ship::where('user_id', $defender_id)->where('type', 'cruiser')->first();
        $nb_def_destroyers = Ship::where('user_id', $defender_id)->where('type', 'destroyer')->first();

        // points attaques liés aux vaisseaux att et def
        $pt_att_fighter = (($nb_att_fighters * 5) * (mt_rand(500, 1500) / 1000));
        $pt_att_frigate = (($nb_att_frigates * 7) * (mt_rand(500, 1500) / 1000));
        $pt_att_cruiser = (($nb_att_cruisers * 9) * (mt_rand(500, 1500) / 1000));
        $pt_att_destroyer = (($nb_att_destroyers * 20) * (mt_rand(500, 1500) / 1000));
        $pt_def_fighter = (($nb_def_fighters->quantity * 5) * (mt_rand(500, 1500) / 1000));
        $pt_def_frigate = (($nb_def_frigates->quantity * 7) * (mt_rand(500, 1500) / 1000));
        $pt_def_cruiser = (($nb_def_cruisers->quantity * 9) * (mt_rand(500, 1500) / 1000));
        $pt_def_destroyer = (($nb_def_destroyers->quantity * 20) * (mt_rand(500, 1500) / 1000));

        // calcule système de combat

        // Round  1 Fighter
        if ($pt_att_fighter > $pt_def_fighter) {
            $reductionfighter = $nb_def_fighters->quantity * 0.3;
            $newQuantity = $nb_def_fighters->quantity - $reductionfighter;
            $newQuantity = ceil($newQuantity);
            $nb_def_fighters->quantity = $newQuantity;
            $nb_def_fighters->save();
            // Round  2 Fighter
            if ($pt_att_fighter > $pt_def_frigate) {
                $reductionfrigate = $nb_def_frigates->quantity * 0.3;
                $newQuantity = $nb_def_frigates->quantity - $reductionfrigate;
                $newQuantity = ceil($newQuantity);
                $nb_def_frigates->quantity = $newQuantity;
                $nb_def_frigates->save();
                // Round  3 Fighter
                if ($pt_att_fighter > $pt_def_cruiser) {
                    $reductioncruiser = $nb_def_cruisers->quantity * 0.3;
                    $newQuantity = $nb_def_cruisers->quantity - $reductioncruiser;
                    $newQuantity = ceil($newQuantity);
                    $nb_def_cruisers->quantity = $newQuantity;
                    $nb_def_cruisers->save();

                    // Round 4 Fighter
                    if ($pt_att_fighter > $pt_def_destroyer) {
                        $reductiondestroyer = $nb_def_destroyers->quantity * 0.3;
                        $newQuantity = $nb_def_destroyers->quantity - $reductiondestroyer;
                        $newQuantity = ceil($newQuantity);
                        $nb_def_destroyers->quantity = $newQuantity;
                        $nb_def_destroyers->save();
                        $win_fighter = "attacker win";
                    } else {
                        $win_fighter  = "round 4";
                        $reductionfighter = $nb_att_fighters * 0.3;
                        $newQuantity = $nb_att_fighters - $reductionfighter;
                        $newQuantity = ceil($newQuantity);
                        $nb_att_fighters = $newQuantity;
                        $att_fighters->quantity = $nb_att_fighters;
                        $att_fighters->save();
                    }
                } else {
                    $win_fighter  = "round 3";
                    $reductionfighter = $nb_att_fighters * 0.3;
                    $newQuantity = $nb_att_fighters - $reductionfighter;
                    $newQuantity = ceil($newQuantity);
                    $nb_att_fighters = $newQuantity;
                    $att_fighters->quantity = $nb_att_fighters;
                    $att_fighters->save();
                }
            } else {
                $win_fighter  = "round 2";
                $reductionfighter = $nb_att_fighters * 0.3;
                $newQuantity = $nb_att_fighters - $reductionfighter;
                $newQuantity = ceil($newQuantity);
                $nb_att_fighters = $newQuantity;
                $att_fighters->quantity = $nb_att_fighters;
                $att_fighters->save();
            }
        } else {
            $win_fighter  = "round 1";
            $reductionfighter = $nb_att_fighters * 0.3;
            $newQuantity = $nb_att_fighters - $reductionfighter;
            $newQuantity = ceil($newQuantity);
            $nb_att_fighters = $newQuantity;
            $att_fighters->quantity = $nb_att_fighters;
            $att_fighters->save();
        }
        // Round  1 Frigate
        if ($pt_att_frigate > $pt_def_fighter) {
            $reductionfighter = $nb_def_fighters->quantity * 0.3;
            $newQuantity = $nb_def_fighters->quantity - $reductionfighter;
            $newQuantity = ceil($newQuantity);
            $nb_def_fighters->quantity = $newQuantity;
            $nb_def_fighters->save();
            // Round  2 Frigate
            if ($pt_att_frigate > $pt_def_frigate) {
                $reductionfrigate = $nb_def_frigates->quantity * 0.3;
                $newQuantity = $nb_def_frigates->quantity - $reductionfrigate;
                $newQuantity = ceil($newQuantity);
                $nb_def_frigates->quantity = $newQuantity;
                $nb_def_frigates->save();

                // Round  3 Frigate
                if ($pt_att_frigate > $pt_def_cruiser) {
                    $reductioncruiser = $nb_def_cruisers->quantity * 0.3;
                    $newQuantity = $nb_def_cruisers->quantity - $reductioncruiser;
                    $newQuantity = ceil($newQuantity);
                    $nb_def_cruisers->quantity = $newQuantity;
                    $nb_def_cruisers->save();

                    // Round 4 Frigate
                    if ($pt_att_frigate > $pt_def_destroyer) {
                        $reductiondestroyer = $nb_def_destroyers->quantity * 0.3;
                        $newQuantity = $nb_def_destroyers->quantity - $reductiondestroyer;
                        $newQuantity = ceil($newQuantity);
                        $nb_def_destroyers->quantity = $newQuantity;
                        $nb_def_destroyers->save();
                        $win_frigate = "attacker win";
                    } else {
                        $win_frigate  = "round 4";
                        $reductionfrigate = $nb_att_frigates * 0.3;
                        $newQuantity = $nb_att_frigates - $reductionfrigate;
                        $newQuantity = ceil($newQuantity);
                        $nb_att_frigates = $newQuantity;
                        $att_frigates->quantity = $nb_att_frigates;
                        $att_frigates->save();
                    }
                } else {
                    $win_frigate  = "round 3";
                    $reductionfrigate = $nb_att_frigates * 0.3;
                    $newQuantity = $nb_att_frigates - $reductionfrigate;
                    $newQuantity = ceil($newQuantity);
                    $nb_att_frigates = $newQuantity;
                    $att_frigates->quantity = $nb_att_frigates;
                    $att_frigates->save();
                }
            } else {
                $win_frigate  = " round 2";
                $reductionfrigate = $nb_att_frigates * 0.3;
                $newQuantity = $nb_att_frigates - $reductionfrigate;
                $newQuantity = ceil($newQuantity);
                $nb_att_frigates = $newQuantity;
                $att_frigates->quantity = $nb_att_frigates;
                $att_frigates->save();
            }
        } else {
            $win_frigate  = "round 1";
            $reductionfrigate = $nb_att_frigates * 0.3;
            $newQuantity = $nb_att_frigates - $reductionfrigate;
            $newQuantity = ceil($newQuantity);
            $nb_att_frigates = $newQuantity;
            $att_frigates->quantity = $nb_att_frigates;
            $att_frigates->save();
        }
        // Round  1 Cruiser
        if ($pt_att_cruiser > $pt_def_fighter) {
            $reductionfighter = $nb_def_fighters->quantity * 0.3;
            $newQuantity = $nb_def_fighters->quantity - $reductionfighter;
            $newQuantity = ceil($newQuantity);
            $nb_def_fighters->quantity = $newQuantity;
            $nb_def_fighters->save();

            // Round  2 Cruiser
            if ($pt_att_cruiser > $pt_def_frigate) {
                $reductionfrigate = $nb_def_frigates->quantity * 0.3;
                $newQuantity = $nb_def_frigates->quantity - $reductionfrigate;
                $newQuantity = ceil($newQuantity);
                $nb_def_frigates->quantity = $newQuantity;
                $nb_def_frigates->save();

                // Round  3 Cruiser
                if ($pt_att_cruiser > $pt_def_cruiser) {
                    $reductioncruiser = $nb_def_cruisers->quantity * 0.3;
                    $newQuantity = $nb_def_cruisers->quantity - $reductioncruiser;
                    $newQuantity = ceil($newQuantity);
                    $nb_def_cruisers->quantity = $newQuantity;
                    $nb_def_cruisers->save();


                    // Round 4 Cruiser
                    if ($pt_att_cruiser > $pt_def_destroyer) {
                        $reductiondestroyer = $nb_def_destroyers->quantity * 0.3;
                        $newQuantity = $nb_def_destroyers->quantity - $reductiondestroyer;
                        $newQuantity = ceil($newQuantity);
                        $nb_def_destroyers->quantity = $newQuantity;
                        $nb_def_destroyers->save();
                        $win_cruiser = "attacker win";
                    } else {
                        $win_cruiser  = " round 4";
                        $reductioncruiser = $nb_att_cruisers * 0.3;
                        $newQuantity = $nb_att_cruisers - $reductioncruiser;
                        $newQuantity = ceil($newQuantity);
                        $nb_att_cruisers = $newQuantity;
                        $att_cruisers->quantity = $nb_att_cruisers;
                        $att_cruisers->save();
                    }
                } else {
                    $win_cruiser  = "round 3";
                    $reductioncruiser = $nb_att_cruisers * 0.3;
                    $newQuantity = $nb_att_cruisers - $reductioncruiser;
                    $newQuantity = ceil($newQuantity);
                    $nb_att_cruisers = $newQuantity;
                    $att_cruisers->quantity = $nb_att_cruisers;
                    $att_cruisers->save();
                }
            } else {
                $win_cruiser  = "round 2";
                $reductioncruiser = $nb_att_cruisers * 0.3;
                $newQuantity = $nb_att_cruisers - $reductioncruiser;
                $newQuantity = ceil($newQuantity);
                $nb_att_cruisers = $newQuantity;
                $att_cruisers->quantity = $nb_att_cruisers;
                $att_cruisers->save();
            }
        } else {
            $win_cruiser  = "round 1";
            $reductioncruiser = $nb_att_cruisers * 0.3;
            $newQuantity = $nb_att_cruisers - $reductioncruiser;
            $newQuantity = ceil($newQuantity);
            $nb_att_cruisers = $newQuantity;
            $att_cruisers->quantity = $nb_att_cruisers;
            $att_cruisers->save();
        }
        // Round  1 Destroyer
        if ($pt_att_destroyer > $pt_def_fighter) {
            $reductionfighter = $nb_def_fighters->quantity * 0.3;
            $newQuantity = $nb_def_fighters->quantity - $reductionfighter;
            $newQuantity = ceil($newQuantity);
            $nb_def_fighters->quantity = $newQuantity;
            $nb_def_fighters->save();

            // Round  2 Destroyer
            if ($pt_att_destroyer > $pt_def_frigate) {
                $reductionfrigate = $nb_def_frigates->quantity * 0.3;
                $newQuantity = $nb_def_frigates->quantity - $reductionfrigate;
                $newQuantity = ceil($newQuantity);
                $nb_def_frigates->quantity = $newQuantity;
                $nb_def_frigates->save();

                // Round  3 Destroyer
                if ($pt_att_destroyer > $pt_def_cruiser) {
                    $reductioncruiser = $nb_def_cruisers->quantity * 0.3;
                    $newQuantity = $nb_def_cruisers->quantity - $reductioncruiser;
                    $newQuantity = ceil($newQuantity);
                    $nb_def_cruisers->quantity = $newQuantity;
                    $nb_def_cruisers->save();

                    // Round 4 Destroyer
                    if ($pt_att_destroyer > $pt_def_destroyer) {
                        $reductiondestroyer = $nb_def_destroyers->quantity * 0.3;
                        $newQuantity = $nb_def_destroyers->quantity - $reductiondestroyer;
                        $newQuantity = ceil($newQuantity);
                        $nb_def_destroyers->quantity = $newQuantity;
                        $nb_def_cruisers->save();
                        $win_destroyer = "attacker win";
                    } else {
                        $win_destroyer  = "round 4";
                        $reductiondestroyer = $nb_att_destroyers * 0.3;
                        $newQuantity = $nb_att_destroyers - $reductiondestroyer;
                        $newQuantity = ceil($newQuantity);
                        $nb_att_destroyers = $newQuantity;
                        $att_destroyers->quantity = $nb_att_destroyers;
                        $att_destroyers->save();
                    }
                } else {
                    $win_destroyer  = "round 3";
                    $reductiondestroyer = $nb_att_destroyers * 0.3;
                    $newQuantity = $nb_att_destroyers - $reductiondestroyer;
                    $newQuantity = ceil($newQuantity);
                    $nb_att_destroyers = $newQuantity;
                    $att_destroyers->quantity = $nb_att_destroyers;
                    $att_destroyers->save();
                }
            } else {
                $win_destroyer  = "round 2";
                $reductiondestroyer = $nb_att_destroyers * 0.3;
                $newQuantity = $nb_att_destroyers - $reductiondestroyer;
                $newQuantity = ceil($newQuantity);
                $nb_att_destroyers = $newQuantity;
                $att_destroyers->quantity = $nb_att_destroyers;
                $att_destroyers->save();
            }
        } else {
            $win_destroyer  = "round 1";
            $reductiondestroyer = $nb_att_destroyers * 0.3;
            $newQuantity = $nb_att_destroyers - $reductiondestroyer;
            $newQuantity = ceil($newQuantity);
            $nb_att_destroyers = $newQuantity;
            $att_destroyers->quantity = $nb_att_destroyers;
            $att_destroyers->save();
        }

        // mise à jour des pertes des vaisseaux



        return response()->json([
            'defender_id' => $defender_id,
            'nb_def_fighter' => $nb_def_fighters,
            'nb_def_frigate' => $nb_def_frigates,
            'nb_def_cruiser' => $nb_def_cruisers,
            'nb_def_destroyer' => $nb_def_destroyers,
            'fuel_needed' =>  $fuel_att_needed,
            'pt_att_fighter' => $pt_att_fighter,
            'pt_att_frigate' => $pt_att_frigate,
            'pt_att_cruiser' => $pt_att_cruiser,
            'pt_att_destroyer' => $pt_att_destroyer,
            'pt_def_fighter' => $pt_def_fighter,
            'pt_def_frigate' => $pt_def_frigate,
            'pt_def_cruiser' => $pt_def_cruiser,
            'pt_def_destroyer' => $pt_def_destroyer,
            'Round_att_fighter' => $win_fighter,
            'Round_att_frigate' => $win_frigate,
            'Round_att_cruiser' => $win_cruiser,
            'Round_att_destroyer' => $win_destroyer,
        ]);
    }
}
