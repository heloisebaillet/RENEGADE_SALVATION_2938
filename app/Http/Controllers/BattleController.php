<?php

namespace App\Http\Controllers;

use App\Models\Battle;
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
        $attackPoints = "";
        $defencePoints = "";
        $ressources = Ressources::class()->type;
        //     $attackFleet = Battle::class()->user;
        //     $defenceFleet = Ships::class()->user_id;
        
        function calculateDistance($x1, $y1, $x2, $y2) {
            return sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));
        }

        function calculateFuel()
        {
   
        }

        function rounds() {

            if ($ships->type == "fighter"){
            $attackPoints ="7";
            $defensePoints="11";
        }
            if ($ships->type == "frigate"){
            $attackPoints ="13";
            $defencePoints="5";
        }
            if ($ships->type == "cruiser"){
            $attackPoints ="14";
            $defensePoints="9";
        }
            if ($ships->type == "destroyer"){
            $attackPoints ="27";
            $defensePoints="20";
        }

        for each round $attackPoints count ++ 
        for each round $defencePoints count -- (first frigate then cruiser then fighter then destroyer)
        
        when $defencePoints -> $ships ==0  from $planetary_system -> $battle over

        $attackPoints -> target first(ships where defencePoints low (ordre croissant))

        }

        function winner (user_id ships ressources planetary_system attacker defender)
        {
            $winner == $attackPoints supérieurs restants when $ships loser == 0

            if winner == attacker : gain 10% from planetary_system ressources defender = $resources_looted
        }

        function loser (ships){
            30% des ships api.destroyed ($ships->quantity * 0.7) restant après battle delete
        }

        if ($ressources == 'fuel' <= $distance) {

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