
document.getElementById('loginForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const matriculaInput = document.getElementById('usuario').value.trim();
    const senhaInput = document.getElementById('senha').value;
    const mensagemDiv = document.getElementById('mensagem');
    const btnEntrar = document.getElementById('btnEntrar');

    mensagemDiv.innerText = "";
    btnEntrar.innerText = "Verificando...";
    btnEntrar.disabled = true;

    const dadosFormulario = new FormData();
    dadosFormulario.append('matricula', matriculaInput);
    dadosFormulario.append('senha', senhaInput);

    try {
        const resposta = await fetch('login.php', {
            method: 'POST',
            body: dadosFormulario
        });

        const texto = await resposta.text();

        console.log("Status HTTP:", resposta.status);
        console.log("Resposta do servidor:", texto);

        const dados = JSON.parse(texto);

        if (dados.sucesso === true && resposta.ok) {
            mensagemDiv.innerText = dados.mensagem;
            mensagemDiv.style.color = "#34d399";

            if (!dados.redirecionar) {
                throw new Error("Destino de redirecionamento não informado.");
            }

            setTimeout(() => {
                window.location.href = dados.redirecionar;
            }, 1500);

        } else {
            mensagemDiv.innerText =
                dados.mensagem || "Não foi possível realizar o login.";

            mensagemDiv.style.color = "#f87171";
            btnEntrar.innerText = "ENTRAR";
            btnEntrar.disabled = false;

            if (dados.sucesso === true && !resposta.ok) {
                console.error("Inconsistência: login aprovado, mas HTTP retornou erro.");
            }
        }

    } catch (erro) {
        console.error("Erro detalhado:", erro);

        mensagemDiv.innerText = "Erro no sistema: " + erro.message;
        mensagemDiv.style.color = "#f87171";

        btnEntrar.innerText = "ENTRAR";
        btnEntrar.disabled = false;
    }
});
