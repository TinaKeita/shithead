<h1>Pieslēgties</h1>
<form method="POST" action="/login">
    @csrf
    <input type="text" name="name" placeholder="Lietotājvārds" required><br>
    <input type="email" name="email" placeholder="E-pasts" required><br>
    <input type="password" name="password" placeholder="Parole" required><br>
    <button type="submit">Ieiet</button>
</form>
<a href="/register">Reģistrēties</a>
@if($errors->any())
    <div style="color:red;">{{ $errors->first() }}</div>
@endif
