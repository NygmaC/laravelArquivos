<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index() {
        $posts = Post::all();
        return view('index', ['posts' => $posts]);
    }

    public function setCadastro(Request $request)  {

        //Parametros para ele armazenar o arquivo
        //=> Primeiro parametro é o nome da pasta la no public do Store
        //=> Segundo parametro é qual dist(qual o local) que ele vai salvar(FileSystem.php (config ))
        $path = $request->file('arquivo')->store('arquivos', 'public');

        $post = new Post();
        $post->email = $request['email'];
        $post->mensagem = $request['mensagem'];
        $post->arquivo =  $path;
        $post->save();

        return redirect('/');
    }

    public function destroy($id) {
        $post = Post::find($id);
        if(isset($post)){
            $arquivo = $post->arquivo;

            if($post->delete()) {
                //Deletando o arquivo da pasta storage 
                Storage::disk('public')->delete($arquivo);    
            }
        }

        return redirect('/');
    }

    public function download($id) {
        $post = Post::find($id);
        if(isset($post)){
            //Pegando o path completo do arquivo
            $path = Storage::disk('public')->getDriver()->getAdapter()->applyPathPrefix($post->arquivo);
            return response()->download($path);
        }

        return redirect('/');
    }
}
