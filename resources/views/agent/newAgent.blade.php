<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro como Agente</title>
</head>
<body>
    <h1>Registro como Agente</h1>
    <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Volver</a>    
    <form action="{{ route('agent.register.store') }}" method="POST" id="agentForm">
        @csrf
        
        <!-- RFC -->
        <div>
            <label>RFC</label>
            <input type="text" name="rfc" id="rfc" 
                   placeholder="AAA000000AAA"
                   pattern="[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}"
                   required>
            <small>Formato: 3-4 letras, 6 números, 3 caracteres</small>
        </div>

        <!-- CURP -->
        <div>
            <label>CURP</label>
            <input type="text" name="curp" id="curp" 
                   placeholder="AAAA000000HAAAAAA00"
                   pattern="[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z]{2}"
                   required>
            <small>Formato: 4 letras, 6 números, 1 letra (H/M), 5 letras, 2 caracteres</small>
        </div>

        <button type="submit">Enviar Solicitud</button>
    </form>

    <script>
        document.getElementById('rfc').addEventListener('input', function(e) {
            const rfc = e.target.value.toUpperCase();
            const pattern = /^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$/;
            
            if (rfc.length > 0 && !pattern.test(rfc)) {
                e.target.style.borderColor = 'red';
            } else {
                e.target.style.borderColor = '';
            }
        });

        document.getElementById('curp').addEventListener('input', function(e) {
            const curp = e.target.value.toUpperCase();
            const pattern = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z]{2}$/;
            
            if (curp.length > 0 && !pattern.test(curp)) {
                e.target.style.borderColor = 'red';
            } else {
                e.target.style.borderColor = '';
            }
        });
    </script>
</body>
</html>