<?php
    session_start();
    include("php/config.php");

    // Processamento do formulário de login quando enviado
    if(isset($_POST['submit'])){
        // Obter o email do formulário e escapar caracteres especiais para evitar injeção de SQL
        $email = mysqli_real_escape_string($con, $_POST['email']);

        // Preparar uma instrução SQL segura para evitar injeção de SQL
        $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE Email=?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        // Obter o resultado da consulta
        $result = mysqli_stmt_get_result($stmt);
        // Obter a linha correspondente como array associativo
        $row = mysqli_fetch_assoc($result);

        // Verificar se há uma linha correspondente e se a senha está correta usando password_verify()
        if($row && password_verify($_POST['password'], $row['Password'])){
            // Definir informações de sessão se as credenciais estiverem corretas
            $_SESSION['valid'] = $row['Email'];
            $_SESSION['username'] = $row['Username'];
            $_SESSION['age'] = $row['Age'];
            $_SESSION['id'] = $row['Id'];
            
            // Verificar se o usuário é um administrador
            if ($row['UserRole'] === 'admin') {
                // Redirecionar para a página de administração se for um administrador
                header("Location: homeadmin.php");
            } else {
                // Redirecionar para a página inicial se não for um administrador
                header("Location: homepage.php");
            }
            // Encerrar o script
            exit();
        } else {
            $error_message = "Email ou senha incorretos";
        }

        // Fechar a instrução preparada
        mysqli_stmt_close($stmt);
    }
?>
<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
</head>
<body>
    <header>
        <h1><a href="index.php">Máquina de Vendas</a></h1>
        <nav>
            <a href="login.php">Login</a>
            <a href="registro.php">Criar Conta</a>
            <a href="sobrenos.php">Sobre Nós</a>
        </nav>
    </header>
    <main>
        <div class="caixa">
            <div class="box form-box">
                <div class="header">Login</div>
               
                <!-- Formulário de login -->
                <form action="" method="post">
                    <!-- Campo de Email -->
                    <div class="field input">
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email" autocomplete="off">
                    </div>
                    <!-- Campo de Senha -->
                    <div class="field input">
                        <label for="password">Palavra-Passe</label>
                        <input type="password" name="password" id="password" autocomplete="off">
                    </div>
                    <!-- Botão de Envio do Formulário -->
                    <div class="field">
                        <input type="submit" class="btn" name="submit" value="Login">
                    </div> 
                </form>
                <?php 
                    // Exibir mensagem de erro se houver
                    if(isset($error_message)){
                        echo "<div class='message'><br>
                              <p>{$error_message}</p>
                              </div><br>";  
                    }
                ?>
                <!-- Links adicionais -->
                <div class="links">
                    Não tens conta? <a href="registro.php">Clica aqui</a>
                </div>
                
                
            </div> 
        </div>
    </main>
</body>
</html>
