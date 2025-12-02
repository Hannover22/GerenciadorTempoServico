<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Campos adicionais</title>

<style>
    .hidden { display: none; }
    .group { margin-top: 10px; }

    /* CHECKBOXES LADO A LADO */
    .checkbox-group {
        display: inline-block;
        margin-right: 20px;
    }
</style>

</head>
<body>

<form>

    <p>
        <label>Nome:</label>
        <input type="text" name="nome_principal">
    </p>

    <!-- CHECKBOXES LADO A LADO -->
    <div>
        <label class="checkbox-group">
            <input type="checkbox" id="chkA">
            Grupo A (Nome, Idade, Bairro)
        </label>

        <label class="checkbox-group">
            <input type="checkbox" id="chkB">
            Grupo B (CPF, RG, Cidade)
        </label>
    </div>

    <br>

    <button type="button" id="btnAdd">Adicionar mais campos</button>


    <!-- GRUPO A -->
    <div id="groupA" class="hidden group">

        <p>
            <label>Nome:</label>
            <input type="text" name="nome_a">
        </p>

        <p>
            <label>Idade:</label>
            <input type="number" name="idade_a">
        </p>

        <p>
            <label>Bairro:</label>
            <input type="text" name="bairro_a">
        </p>

    </div>

    <!-- GRUPO B -->
    <div id="groupB" class="hidden group">

        <p>
            <label>CPF:</label>
            <input type="text" name="cpf_b">
        </p>

        <p>
            <label>RG:</label>
            <input type="text" name="rg_b">
        </p>

        <p>
            <label>Cidade:</label>
            <input type="text" name="cidade_b">
        </p>

    </div>


    <p>
        <button type="submit">Enviar</button>
    </p>

</form>


<script>
document.getElementById("btnAdd").addEventListener("click", function() {

    const chkA = document.getElementById("chkA").checked;
    const chkB = document.getElementById("chkB").checked;

    // Grupo A
    if (chkA) {
        document.getElementById("groupA").classList.remove("hidden");
    } else {
        document.getElementById("groupA").classList.add("hidden");
    }

    // Grupo B
    if (chkB) {
        document.getElementById("groupB").classList.remove("hidden");
    } else {
        document.getElementById("groupB").classList.add("hidden");
    }

});
</script>

</body>
</html>
