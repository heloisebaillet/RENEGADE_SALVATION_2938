<?php

namespace App\Http\Controllers;

use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StructureController extends Controller
{
    // creer un batiment par rapport a ses ressources et à son énergie
    public function create(Request $request, $type = null){
        // A modifier, quand le controller User sera créé
        $user_id= "1";
        $level="1";
        // A vérifier si l'utilisateur a assez d'énergie
        if ($type == "mine" || $type == "raffinery" || $type == "centrale"){
        // Création d'une mine
            if ($type == "mine"){
            // A verifier si l'utilisateur a assez de minerais avec un if
                $energy_consumption ="1";
                $mine = new Structure();
                $mine->user_id = $user_id;
                $mine->type = $type;
                $mine->level = $level;
                $mine->energy_consumption = $energy_consumption;
                $mine->save();
                return Response()->json($mine, 201);}

            if ($type == "raffinery"){
                    // A verifier si l'utilisateur a assez de minerais avec un if
                $energy_consumption ="2";
                $raffinery = new Structure();
                $raffinery->user_id = $user_id;
                $raffinery->type = $type;
                $raffinery->level = $level;
                $raffinery->energy_consumption = $energy_consumption;
                $raffinery->save();
                return Response()->json($raffinery, 201);}
                
            if ($type == "centrale"){
                    // A verifier si l'utilisateur a assez de minerais avec un if
                $energy_consumption ="0";
                $centrale = new Structure();
                $centrale->user_id = $user_id;
                $centrale->type = $type;
                $centrale->level = $level;
                $centrale->energy_consumption = $energy_consumption;
                $centrale->save();
                return Response()->json($centrale, 201);} }     
        else {
            return Response()->json(['success' =>'false'], 423);}
        }
    // lit les batiments de l'utilisateur
    public function read (Request $request, $type = null){
        // A modifier, quand le controller User sera créé
        $user_id ="1";
        // si le paramètre n'est pas vide, choix du type de batiment
        if ($type != null){
                $choice = Structure::where('user_id', $user_id)->where('type', $type)->get();
                 return response()->json($choice,200) ;
            }
            
        else{
            $all = Structure::where('user_id', $user_id)->get();
                return response()->json($all, 200) ;
        }
         
    }
    // Ajoute level +1 quand les ressources le permette
    public function addlevel(Request $request, $id){
        // A modifier, quand le controller User sera créé
        $user_id ="1";
        $minerais = 300;
        $type = Structure::where('user_id', $user_id)->where('id', $id)->first();
        if ($type != ""){
            if ($minerais >= 300){
                
                $type->level += 1;
                $type->save();
                
                    return Response()->json( $type, 201);
            }
            else {
                return Response()->json(['success' =>'false'], 400);}
            }
            else {
                return Response()->json(['success' =>'false'], 400);}

    }
    public function delete(Request $request, $id = null){
        // A modifier, quand le controller User sera créé
        $user_id ="1";
        $type = Structure::where('user_id', $user_id)->where('id', $id)->first();

        if ($type != "" && $user_id == $type->user_id){
            $type->delete();
            return Response()->json(['success' =>'true'], 204);
         }
         // si déja supprimé :
         else if ($type == ""){
            return Response()->json(['success' =>'false'], 400);}
         

         //si pas autorisé
         else  {
            return Response()->json(['success' =>'false'], 403);}

        // Ajouter après la création du controller ressources,
        // la réinsertion de 50% de la fabrication.
}
}
