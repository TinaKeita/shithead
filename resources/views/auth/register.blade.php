<h1>Reģistrēties</h1>
<form method="POST" action="/register">
    @csrf
    <input type="text" name="name" placeholder="Lietotājvārds" required><br>
    <input type="email" name="email" placeholder="E-pasts" required><br>
    <input type="password" name="password" placeholder="Parole" required><br>
    <button type="submit">Reģistrēties</button>
</form>
<a href="/login">Ieiet</a>
@if($errors->any())
    <div style="color:red;">{{ $errors->first() }}</div>
@endif
