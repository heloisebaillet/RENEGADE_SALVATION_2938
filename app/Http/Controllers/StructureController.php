<?php

namespace App\Http\Controllers;

use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StructureController extends Controller
{
    // creer un batiment par rapport a ses ressources et à son énergieS
    public function create(Request $request, $type = null){
        // A modifier, quand le controller User sera créé
        $user_id= "1";
        $level="1";
        // A vérifier si l'utilisateur a assez d'énergie
        if ($type == "mine" || $type == "raffinery" || $type == "centrale"){
        // Création d'une mine
            if ($type == "mine"){
            // A verifier si l'utilisateur a assez de minerais avec un if
                $energy_consumption ="300";
                $mine = new Structure();
                $mine->user_id = $user_id;
                $mine->type = $type;
                $mine->level = $level;
                $mine->energy_consumption = $energy_consumption;
                $mine->save();
                return Response()->json(['Nouveau batiment créé ! Type: '.($type).'' => $mine], 201);}

            if ($type == "raffinery"){
                    // A verifier si l'utilisateur a assez de minerais avec un if
                $energy_consumption ="300";
                $raffinery = new Structure();
                $raffinery->user_id = $user_id;
                $raffinery->type = $type;
                $raffinery->level = $level;
                $raffinery->energy_consumption = $energy_consumption;
                $raffinery->save();
                return Response()->json(['Nouveau batiment créé ! Type: '.($type).'' => $raffinery], 201);}
                
            if ($type == "centrale"){
                    // A verifier si l'utilisateur a assez de minerais avec un if
                $energy_consumption ="500";
                $centrale = new Structure();
                $centrale->user_id = $user_id;
                $centrale->type = $type;
                $centrale->level = $level;
                $centrale->energy_consumption = $energy_consumption;
                $centrale->save();
                return Response()->json(['Nouveau batiment créé ! Type: '.($type).'' => $centrale], 201);} }     
        else {
            return Response()->json(['on ne force pas votre URL! désolé'], 403);}
        }
    // lit les batiments de l'utilisateur
    public function read (Request $request, $type = null){
        // A modifier, quand le controller User sera créé
        $user_id ="1";
        // si le paramètre n'est pas vide, choix du type de batiment
        if ($type != null){
                $choise = Structure::where('user_id', $user_id)->where('type', $type)->get();
                 return response()->json(['Liste des '.($type).'  user n° '.($user_id).'', $choise]) ;
            }
            
        else{
            $all = Structure::where('user_id', $user_id)->get();
                return response()->json(['Liste des Batiments user n° '.($user_id).'', $all]) ;
        }
         
    }
    // Ajoute level +1 quand les ressources le permette
    public function addlevel(Request $request, $id){
        // A modifier, quand le controller User sera créé
        $user_id ="1";
        $minerais = 300;

        if ($minerais >= 300){
            $mine = Structure::where('user_id', $user_id)->where('id', $id)->first();
            $mine->level += 1;
            $mine->save();
              
                return Response()->json(['Votre batiment n° '.($id).' à évolué', $mine]);
        }
        else {
            return Response()->json(['pas assez de minerais! désolé'], 403);        }

    }
    public function delete(Request $request, $id = null){
        // A modifier, quand le controller User sera créé
        $user_id ="1";
        $mine = Structure::where('user_id', $user_id)->where('id', $id)->first();

        if ($mine != "" && $user_id == $mine->user_id){
            $mine->delete();
            return Response()->json(['Votre '.($mine->type).' n° '.($id).' à été supprimé'], 201);
         }
         // si déja supprimé :
         else if ($mine == ""){
            return Response()->json(['Action non autorisé'], 403);}
         

         //si pas autorisé
         else  {
            return Response()->json(['Action non autorisé'], 403);}

        // Ajouter après la création du controller ressources,
        // la réinsertion de 50% de la fabrication.
}
}
