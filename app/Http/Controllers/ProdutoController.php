<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use Storage, Session;
use Spatie\Dropbox\Client;

class ProdutoController extends Controller
{
    public function gerenciar_produtos(Request $request) {
        if ($request->input('operacao') == 'Adicionar') {
            $imagem = $request->file('imagem');
            $arquivo = $imagem->get();
            $nome_arquivo = $imagem->getClientOriginalName();
            
            $produto = new Produto();
            $produto->nome = $request->input('nome');
            if ($request->input('tipo-especifico') <> '') {
                $produto->tipo = $request->input('tipo') .'_'. $request->input('tipo-especifico');
            } else {
                $produto->tipo = $request->input('tipo');
            }
            $produto->preco = $request->input('preco');
            $produto->nome_arquivo = $nome_arquivo;

            // API Dropbox
            try {
                $client = new \GuzzleHttp\Client();
                $res = $client->request("POST", "https://".env('DROPBOX_APP_KEY').":".env('DROPBOX_APP_SECRET')."@api.dropbox.com/oauth2/token", [
                    'form_params' => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => env('DROPBOX_APP_REFRESH_TOKEN'),
                    ]
                ]);
                if ($res->getStatusCode() == 200) {
                    $access_token_request = json_decode($res->getBody());
                    $access_token = $access_token_request->access_token;
                } else {
                    return false;
                }
            }
            catch (Exception $e) {
                $this->logger->error("[{$e->getCode()}] {$e->getMessage()}");
                return false;
            }
            
            $client = new Client($access_token);
            $caminho_dropbox = '/images/'.$nome_arquivo;
            $client->upload($caminho_dropbox, $arquivo, $mode='add');
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.dropboxapi.com/2/sharing/create_shared_link_with_settings');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"path\":\"/images/".$produto->nome_arquivo."\"}");
            $headers = array();
            $headers[] = 'Authorization: Bearer ' . $access_token;
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            $json = json_decode($result);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);

            $url = str_replace('dl=0', 'raw=1', $json->url);
            $produto->caminho_imagem = str_replace('https://www.dropbox.com/s/', '', $url);
            
            $produto->save();
            Session::flash('sucesso', 'Produto adicionado com sucesso.');
            return redirect('gerenciar-cardapio');
        } elseif ($request->input('operacao') == 'Remover') {
            try {
                $client = new \GuzzleHttp\Client();
                $res = $client->request("POST", "https://".env('DROPBOX_APP_KEY').":".env('DROPBOX_APP_SECRET')."@api.dropbox.com/oauth2/token", [
                    'form_params' => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => env('DROPBOX_APP_REFRESH_TOKEN'),
                    ]
                ]);
                if ($res->getStatusCode() == 200) {
                    $access_token_request = json_decode($res->getBody());
                    $access_token = $access_token_request->access_token;
                } else {
                    return false;
                }
            }
            catch (Exception $e) {
                $this->logger->error("[{$e->getCode()}] {$e->getMessage()}");
                return false;
            }

            $produtos = Produto::all();
            foreach ($produtos as $produto) {
                if ($produto->id == $request->input('id')) {
                    //unlink($produto->caminho_imagem);

                    // API Dropbox
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://api.dropboxapi.com/2/files/delete_v2');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"path\":\"/images/".$produto->nome_arquivo."\"}");
                    $headers = array();
                    $headers[] = 'Authorization: Bearer ' . $access_token;
                    $headers[] = 'Content-Type: application/json';
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    $result = curl_exec($ch);
                    if (curl_errno($ch)) {
                        echo 'Error:' . curl_error($ch);
                    }
                    curl_close($ch);
                    //

                    $produto->delete();
                    Session::flash('sucesso', 'Produto removido com sucesso.');
                } else {
                    Session::flash('erro', 'Não existe produto com esse nome.');
                }
            }
            return redirect('gerenciar-cardapio');
        }
    }
}
