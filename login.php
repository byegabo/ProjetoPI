<?php
include('conexao.php');

$erro = '';
if(isset($_POST['email'])  || isset($_POST['senha'])) {

    if(strlen($_POST['email']) == 0) {
        $erro = "Preencha seu e-mail";
    } else if(strlen($_POST['senha']) == 0) {
        $erro = "Preencha sua senha";
    } else {

        $email = $mysqli->real_escape_string($_POST['email']);
        $senha = $mysqli->real_escape_string($_POST['senha']);

        $sql_code = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";
        $sql_query = $mysqli->query($sql_code) or die("Falha na execução do código SQL: " . $mysqli->error);

        $quantidade = $sql_query->num_rows;

        if($quantidade == 1) {

            $usuario = $sql_query->fetch_assoc();

            if(!isset($_SESSION)) {
                session_start();
            }

            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];

            header("Location: pagina_inicial.php");

        } else {
            $erro = "Falha ao logar! email ou senha incorretos";
        }

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <form action="" method="POST">
    <div class="tela_login">
        <h1>Login</h1>
        <input type="text" name="email" placeholder="Email">
        <br><br>
        <input type="password" name="senha" placeholder="Senha">
        <br><br>
        <button type="submit"> Enviar</button>
        
        <p class="erro"><?php echo $erro; ?></p>
    </div>
    </form>
</body>
</html>