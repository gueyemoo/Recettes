<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Like;
use Auth;


class RecipesController extends Controller
{
  function index(Request $request) {


      if($titleRecipe = $request->segment(2) != null){
        $recipe = \App\Models\Recipe::where('title',$titleRecipe)->first(); //recupere une recette en fonction de son titre
        $recipe = $this.show($recipe);

    }else{
      return view('recipes');
    }
  }

  public function show($recipe) {
   $author = $this->getUserById($recipe->author_id);

   return view('recipesShow')
            ->with('recipe', $recipe)
            ->with('author', $author);
  }

  // Save Comment
  function save_comment(Request $request){
      if ($request->comment) {
        $data=new \App\Models\Comment;
        $data->recipe_id=$request->segment(3);
        $data->author_id = Auth::user()->id  ;
        $data->content=$request->comment;
        $data->date= date("Y-m-d H:i:s");
        $data->save();
        return response()->json([
            'bool'=>true
        ]);   
       }
       return response()->json([
           'bool'=>false
       ]);

  }

  //funtions pour likes
  public function getlike(Request $request)
  {
      $recipe = Recipe::find($request->recipe);
      return response()->json([
          'recipe'=>$recipe,
      ]);
  }

  public function like(Request $request)
  {
    $recipe = \App\Models\Recipe::where('id',$request->segment(4))->first(); //recupere une recette en fonction de son id
    $value = $recipe->like;
    $likeExistant = \App\Models\Like::where('author_id', Auth::user()->id)
        ->where('recipe_id', $recipe->id)->first();
    //verif si utilisateur a deja like la recette
    if ($likeExistant) {
        $likeExistant->delete();
        $recipe->like = $value-1;                  
                           
    } else {
        $like = new \App\Models\Like;
        $like->recipe_id = $recipe->id;
        $like->author_id = Auth::user()->id;
        $like->save();
    
        $value = $recipe->like;
        $recipe->like = $value+1;
    }
    $recipe->save();
    $author = $this->getUserById($recipe->author_id);

      return view('recipesShow')
               ->with('recipe', $recipe)
               ->with('author', $author);
  }    



  // Recupere l'objet utilisateur via son id
  public function getUserById($id){
      $user =  \App\Models\User::where('id',$id)->first();
      return $user;
  }

}
