@extends('layouts.master')
@section('title', 'Gerenciar Produtos')

@section('content')

<?php 
if (Session::get('admin')) {
?>

<!-- Navbar -->
<nav style="position: relative !important;" id="nav" class="text-center navbar navbar-expand-lg navbar-light bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="/">Pesqueiro Canaã</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <?php 
    	if (Session::get('admin')) {
    ?>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="/gerenciar-cardapio">Gerenciar cardápio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/gerenciar-admins">Gerenciar admins</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/logout">Logout</a>
        </li>
      </ul>
    </div>
	<?php } ?>
  </div>
</nav>

<div class="container">
	<br>
	<?php
		if (Session::get('sucesso')) {
			echo ('	<div class="alert alert-success" role="alert">' .
						Session::get('sucesso') .
					'</div>');
		} elseif (Session::get('erro')) {
			echo ('	<div class="alert alert-danger" role="alert">' .
						Session::get('erro') .
					'</div>');
		}

		echo ('<p style="text-align: right;">Usuário logado: <i>' .Session::get('admin'). '</i></p>');
	?>
	<h1 class="text-center">Gerenciar Cardápio</h1>
	<br>
	<div class="row form-group">
		<div class="col">
			<h3 class="text-center">Adicionar produto</h3>
			<center><hr></center>
			<form method="post" action="" enctype="multipart/form-data">
				@csrf
				<label for="nome">Nome</label>
				<input class="form-control" type="text" name="nome" required>
				<br>
				<label for="tipo">Tipo</label>
				<select class="form-control" onchange="" id="tipo" name="tipo" required>
					<option value="Porção">Porção</option>
					<option value="Combo">Combo</option>
					<option value="Prato Executivo">Prato Executivo</option>
					<option value="Bebida">Bebida</option>
					<option value="Suco">Suco</option>
				</select>
				<br>
				<div id="tipo-especifico"></div>
				<label for="preco">Preço</label>
				<div class="input-group">
        			<div class="input-group-prepend">
          				<div class="input-group-text">R$</div>
        			</div>
        			<input class="form-control" type="text" name="preco" required>
      			</div>
				<br>
				<label for="imagem">Imagem</label>
				<input class="form-control" type="file" accept=".jpeg, .jpg, .png" name="imagem" required>
				<br>
				<center><input class="btn btn-success" name="operacao" type="submit" value="Adicionar"></center>
			</form>
		</div>
	</div>
	<br><br>
	<div class="row form-group">
		<div class="col">
			<h3 class="text-center">Remover produto</h3>
			<center><hr></center>
			<form method="post" action="" enctype="multipart/form-data">
				@csrf
				<label for="nome">Nome</label>
				<input class="form-control" type="text" name="nome" required>
				<br>
				<center><input class="btn btn-danger" name="operacao" type="submit" value="Remover"></center>
			</form>
		</div>
	</div>
	<br>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="http://localhost:8000/js/gerenciar_cardapio.js"></script>
</div>

<?php
} else {
	echo (	'<div class="alert alert-danger container" role="alert">
				Acesso negado.
			</div><br>');
}
?>

@stop