<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id'])) {
    header("Location: logecad.php");
    exit();
}

$erro = '';
$sucesso = '';

$userId = $_SESSION['id'];
$sql = "SELECT * FROM usuarios WHERE id = $userId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
} else {
    die("Usuário não encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['nick'], $_POST['tags'], $_POST['bio'], $_POST['discord'])) {
        $nick = $conn->real_escape_string($_POST['nick']);
        $tags = $_POST['tags'];
        $bio = $conn->real_escape_string($_POST['bio']);
        $discord = $conn->real_escape_string($_POST['discord']);

        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["foto_perfil"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $uploadOk = 1;

            $check = getimagesize($_FILES["foto_perfil"]["tmp_name"]);
            if ($check === false) {
                $erro = "O arquivo não é uma imagem.";
                $uploadOk = 0;
            }

            if ($_FILES["foto_perfil"]["size"] > 500000) {
                $erro = "O arquivo é muito grande.";
                $uploadOk = 0;
            }

            if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                $erro = "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
                $uploadOk = 0;
            }

            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $target_file)) {
                    $foto_perfil = $target_file;

                    $sql = "UPDATE usuarios SET foto_perfil = '$foto_perfil' WHERE id = $userId";
                    $conn->query($sql);
                    $_SESSION['foto_perfil'] = $foto_perfil;
                } else {
                    $erro = "Erro ao fazer upload da imagem.";
                }
            }
        }

        $sql = "UPDATE usuarios SET nick = '$nick', bio = '$bio', discord = '$discord' WHERE id = $userId";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['nome'] = $nick;
            $_SESSION['bio'] = $bio;
            $_SESSION['discord'] = $discord;
        } else {
            $erro = "Erro ao atualizar o nick: " . $conn->error;
        }

        $sql = "DELETE FROM usuario_tags WHERE usuario_id = $userId";
        $conn->query($sql);

        foreach ($tags as $tagId) {
            $tagId = (int)$tagId;
            $sql = "INSERT INTO usuario_tags (usuario_id, tag_id) VALUES ($userId, $tagId)";
            $conn->query($sql);
        }

        $sucesso = "Dados atualizados com sucesso.";
    }

    if (isset($_POST['senha_atual'], $_POST['nova_senha'], $_POST['confirmar_senha'])) {
        if (!empty($_POST['senha_atual'])) {
            $senha_atual = $_POST['senha_atual'];
            $nova_senha = $_POST['nova_senha'];
            $confirmar_senha = $_POST['confirmar_senha'];

            if (password_verify($senha_atual, $usuario['senha'])) {
                if ($nova_senha === $confirmar_senha) {
                    $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                    $sql = "UPDATE usuarios SET senha = '$nova_senha_hash' WHERE id = $userId";
                    if ($conn->query($sql) === TRUE) {
                        $sucesso = "Senha atualizada com sucesso.";
                    } else {
                        $erro = "Erro ao atualizar a senha: " . $conn->error;
                    }
                } else {
                    $erro = "As novas senhas não coincidem.";
                }
            } else {
                $erro = "Senha atual incorreta.";
            }
        }
    }

    if (isset($_FILES['wallpaper']) && $_FILES['wallpaper']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["wallpaper"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $uploadOk = 1;

        $check = getimagesize($_FILES["wallpaper"]["tmp_name"]);
        if ($check === false) {
            $erro = "O arquivo não é uma imagem.";
            $uploadOk = 0;
        }

        if ($_FILES["wallpaper"]["size"] > 5000000) { 
            $erro = "O arquivo é muito grande.";
            $uploadOk = 0;
        }

        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            $erro = "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["wallpaper"]["tmp_name"], $target_file)) {
                $wallpaper = $target_file;
                $sql = "UPDATE usuarios SET wallpaper = '$wallpaper' WHERE id = $userId";
                $conn->query($sql);
                $_SESSION['wallpaper'] = $wallpaper;
            } else {
                $erro = "Erro ao fazer upload do wallpaper.";
            }
        }
    }

    header("Location: perfil.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    
    <style>
         @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

        body {
            background-color: #f9f9f9; 
            font-family: 'Roboto', sans-serif; 
            margin: 0;
            padding: 20px;
            color: #333; 
        }
        .configuracao {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.1); 
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
            padding: 15px;
            padding-bottom: 3px;
            max-width: 800px;
            text-align: left;
            margin-bottom: 15px;
        }

        .configuracao h2 {
            font-size: 1.5rem;
            margin-bottom: 5px;
            color: #222; 
            
        }

        .configuracao p {
            font-size: 1rem;
            line-height: 1.5;
            color: #555; 
            display: inline-block;
        }

        .input-button {
            display: inline-block;
            background: #9147ff; 
            color: #fff;
            border: none;
            border-radius: 8px; 
            padding: 10px 15px; 
            font-size: 1rem; 
            cursor: pointer;
            text-align: center; 
            margin-top: 10px;
        }

        .input-button:hover {
            background: #772ce8; 
        }

        .input-button:focus {
            outline: 2px solid #9147ff; 
        }

        button {
            display: inline-block;
            background: #9147ff; 
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 15px 15px; 
            font-size: 1rem; 
            cursor: pointer; 
            text-align: center; 
            margin-top: 10px;
        }

        button:hover{
            background: #772ce8; 
        }

        button:focus{
            outline: 2px solid #9147ff;
        }
        .fotoperfil {
            color: white;
            padding: 4px;
            background-color: blueviolet;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.5);
        }
        img {
            padding-bottom: 5px;
        }
        .texto {
            width: 50%;
            padding: 10px 15px; 
            font-size: 1rem; 
            color: #333; 
            background-color: #ffffff;
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
            outline: none;
            transition: all 0.3s ease; 
        }

        .texto:focus {
            border-color: #9147ff; 
            box-shadow: 0 2px 6px rgba(145, 71, 255, 0.4); 
        }
        .select {
            width: 100%;
            padding: 10px 15px;
            font-size: 1rem;
            color: #333;
            background-color: #ffffff;
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
            appearance: none; 
            outline: none; 
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
            transition: all 0.3s ease; 
            cursor: pointer;
        }

        .select:focus {
            border-color: #9147ff; 
            box-shadow: 0 2px 6px rgba(145, 71, 255, 0.4); 
        }

        .select option {
            color: #333; 
            background-color: #ffffff;
        }
    </style>
</head>
<body>
    <h1>Editar Perfil</h1>

    <?php if ($erro): ?>
        <div style="color: red;"><?php echo $erro; ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <div style="color: green;"><?php echo $sucesso; ?></div>
    <?php endif; ?>
    <div class="configuracao">
    <form method="POST" enctype="multipart/form-data">
        <h2>Alterar Foto de Perfil</h2>
        <img src="<?php echo $usuario['foto_perfil']; ?>" alt="Foto de Perfil" style="width: 100px; height: 100px; border-radius: 50%;"><br>
        <label class="fotoperfil" for="foto">Alterar Imagem de Perfil</label><br> 
        <input style="display: none;" id="foto" type="file" name="foto_perfil" accept="image/*"><br><br>
    </div>
    <div class="configuracao">    
        <h2>Alterar Wallpaper</h2>
        <img src="<?php echo $usuario['wallpaper']; ?>" alt="Wallpaper" style="width: 300px; height: 150px;"><br>
        <label class="fotoperfil" for="wallpaper">Alterar Wallpaper</label><br> 
        <input style="display:none " id=wallpaper type="file" name="wallpaper" accept="image/*"><br><br>
    </div>
    <div class="configuracao">   
        <h2>Alterar Nick</h2>
        <input type="text" class="texto" name="nick" value="<?php echo $usuario['nick']; ?>" required><br><br>
    </div>
    <div class="configuracao">
        <h2>Bio</h2>
        <textarea name="bio" class="texto" rows="5" placeholder="Escreva sua bio..." ><?php echo $usuario['bio'] ?? ''; ?></textarea><br><br>
    </div>
    <div class="configuracao">
        <h2>Discord</h2>
        <textarea name="discord" class="texto" rows="1" placeholder="Insira seu nick do Discord" ><?php echo $usuario['discord'] ?? ''; ?></textarea><br><br>
    </div>
    <div class="configuracao">
        <h2>Selecionar Tags</h2>
        <div style="display: flex;">
            <div style="margin-right: 20px;">
                <h3>Tags Disponíveis</h3>
                <select class="select" name="tags[]" multiple size="8">
                    <?php
                    $tagsSql = "SELECT * FROM tags";
                    $tagsResult = $conn->query($tagsSql);

                    while ($tag = $tagsResult->fetch_assoc()) {
                        echo "<option value='" . $tag['id'] . "'>" . $tag['nome'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <h3>Tags Selecionadas</h3>
                <select class="select" name="tags[]" multiple size="8">
                    <?php
                    $userTagsSql = "SELECT tag_id FROM usuario_tags WHERE usuario_id = $userId";
                    $userTagsResult = $conn->query($userTagsSql);
                    $userTags = [];
                    while ($row = $userTagsResult->fetch_assoc()) {
                        $userTags[] = $row['tag_id'];
                    }

                    foreach ($userTags as $tagId) {
                        $tagSql = "SELECT nome FROM tags WHERE id = $tagId";
                        $tagResult = $conn->query($tagSql);
                        $tag = $tagResult->fetch_assoc();
                        echo "<option value='" . $tagId . "' selected>" . $tag['nome'] . "</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        </div>
        </div>
    <div class="configuracao">    
        <h2>Alterar Senha</h2>
        <input type="password" class="texto" name="senha_atual" placeholder="Senha Atual"><br><br>
        <input type="password" class="texto" name="nova_senha" placeholder="Nova Senha"><br><br>
        <input type="password" class="texto" name="confirmar_senha" placeholder="Confirmar Nova Senha"><br><br>
        </div>
        <button type="submit">Salvar Alterações</button>
    </div>
    </form>
</body>
</html>
