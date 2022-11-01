<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use Storage, Session;

class ProdutoController extends Controller
{
    public function gerenciar_produtos(Request $request) {
        if ($request->input('operacao') == 'Adicionar') {
            $imagem = $request->file('imagem');
            $nome_arquivo = $imagem->getClientOriginalName();
            $caminho_imagem = $imagem->storeAs('images', $nome_arquivo, 'public');

            $produto = new Produto();
            $produto->nome = $request->input('nome');
            $produto->tipo = $request->input('tipo');
            $produto->tipo_especifico = $request->input('tipo-especifico');
            $produto->preco = $request->input('preco');
            $produto->caminho_imagem = $caminho_imagem;
            $produto->nome_arquivo = $nome_arquivo;
            //$produto->imagem = $imagem;

            $produto->save();
            Session::flash('sucesso', 'Produto adicionado com sucesso.');
            return redirect('gerenciar-produtos');
        } elseif ($request->input('operacao') == 'Remover') {
            $produtos = Produto::all();
            foreach ($produtos as $produto) {
                if ($produto->nome == $request->input('nome')) {
                    $produto->delete();
                    Session::flash('sucesso', 'Produto removido com sucesso.');
                } else {
                    Session::flash('erro', 'Não existe produto com esse nome.');
                }
            }
            return redirect('gerenciar-produtos');
        }
    }

    public function get_produtos() {
        $produtos = json_encode(Produto::all(), JSON_UNESCAPED_UNICODE);
        return response($produtos, 200);
    }
}
