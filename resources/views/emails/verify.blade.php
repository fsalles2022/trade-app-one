<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>

<div>
    Olá {{ $name }} - {{ $cpf }},
    <br>
    @if (isset($userExists) && $userExists === false)
        Estamos felizes em ter você no Trade App One, não esqueça terminar seu
        registro!
    <br>
        <h3>Sua senha provisoria: {{ $hashedPassword }}</h3>
    <br>
        Clique <a href="{{ config('mail.userRegisteredMailUrl') }}">aqui</a> para efetuar o seu primeiro acesso com a sua senha provisoria!
    @else
        Sua senha foi alterada com sucesso, utilize sua senha provisória para efetuar o login
    <br>
        <h3>Sua senha provisoria: {{ $hashedPassword }}</h3>
    <br>
        Clique <a href="{{ config('mail.userRegisteredMailUrl') }}">aqui</a> para efetuar o seu acesso com a sua senha provisoria!
    @endif
    <br>
        Caso nao seja redirecionado ao clicar acesse {{ config('mail.userRegisteredMailUrl') }}
    <br/>
</div>

</body>
</html>
