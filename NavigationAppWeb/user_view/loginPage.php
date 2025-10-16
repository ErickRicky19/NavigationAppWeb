<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <title>Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 20px 100px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 99;
        }
        .logo{
            font-size: 2em;
            color: #fff;
            user-select: none;
        }
        .navigation a{
            position: relative;
            font-size: 1.1em;
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            margin-left: 40px;
        }
        .navigation .login-button{
            width: 130px;
            height: 50px;
            background: transparent;
            border: 2px solid #fff;
            outline: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.1em;
            color: #fff;
            font-weight: 500;
            margin-left: 40px;
        }
        .navigation .login-button:hover{
            background: #fff;
            color: #162938;
        }
        .navigation a::after{
            content: '';
            position: absolute;
            left: 0;
            bottom: -6px;
            width: 100%;
            height: 3px;
            background: #fff;
            border-radius: 5px;
            transform-origin: right;
            transform: scaleX(0);
            transition: transform .5s;
        }
        .navigation a:hover::after{
            transform: scaleX(1);
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('../soruce/background.jpg') no-repeat;
            background-size: cover;
            background-position: center;
        }
        .wrapper {
            position: relative;
            width: 400px;
            height: 440px;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            backdrop-filter: blur(20px);
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .wrapper .form-box {
            width: 100%;
            padding: 40px;
        }
        .form-box h2 {
            font-size: 2em;
            color: #162938;
            text-align: center;
        }
        .input-box {
            position: relative;
            width: 100%;
            height: 50px;
            border-bottom: 2px solid #162938;
            margin: 30px 0;
        }
        .input-box label {
            position: absolute;
            top: 50%;
            left: 10px;
            color: #162938;
            font-size: 1em;
            pointer-events: none;
            transform: translateY(-50%);
            transition: .5s;
        }
        .input-box input:focus ~ label,
        .input-box input:valid ~ label {
            top: -5px;
            color: #162938;
            font-size: 0.8em;
        }
        .input-box input {
            width: 100%;
            height: 100%;
            background: transparent;
            border: none;
            outline: none;
        }
        .input-box .icon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            font-size: 1.2em;
            color: #162938;
        }
        .btn {
            width: 100%;
            height: 40px;
            background: #162938;
            border: none;
            outline: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            color: #fff;
        }
        .btn:hover {
            background: #0d1b26;
            color: #fff;
        }
        .register-link {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9em;
            color: #162938;
        }
        .register-link a {
            color: #162938;
            text-decoration: none;
            font-weight: 600;
        }
        .register-link a:hover { text-decoration: underline; }
       
    </style>
</head>
<body>
    <header>
        <h1 class="logo">Navigation App Web!</h1>
        <nav class="navigation">
            <a href="#">About</a>
            <a href="#">Contact</a>
        </nav>
    </header>
    <div class="wrapper">
        <div class="form-box login">
    <form method="POST" action="../core/login.php">
    <h2>Login</h2>

    <?php if (isset($_SESSION['create'])): ?>
        <p style="color: green;"><?php echo htmlspecialchars($_SESSION['create']); ?></p>
        <?php unset($_SESSION['create']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error']); ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <div class="input-box">
    <span class="icon"><ion-icon name="people-outline"></ion-icon></span>
    <input type="text" id="username" name="username" required>
    <label for="username">Username:</label>
    <br><br>
    </div>
    <div class="input-box">
    <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
    <input type="password" id="password" name="password" required>
    <label for="password">Password:</label>
    <br><br> 
    </div>
    <button class="btn" type="submit">Login</button>
    <div class="register-link">
    <p>Don't have an account? <a href="registerPage.php">Create one here</a></p>
    </div>
</form>
        </div>
    </div>
</body>
</html>
