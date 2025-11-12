<?php
// Restringe acesso: só permite usuários autenticados (senão redireciona para /auth/login.php)
require_once __DIR__ . '/../../app/guards/auth_guard.php';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Novo Livro</title>
  <!-- CSS global do projeto -->
  <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
  <div class="page-card"><!-- Card central com sombra/padding -->
    <h1>Novo Livro</h1>

    <!-- novalidate: deixamos o JS controlar mensagens customizadas
         method=POST: envia dados sem expor na URL
         action: rota que persiste no banco -->
    <form method="POST" action="/livros/store.php" novalidate>
      <!-- Campo obrigatório: Título -->
      <label>Título
        <input type="text" name="titulo" required>
      </label>

      <!-- Campo obrigatório: Autor -->
      <label>Autor
        <input type="text" name="autor" required>
      </label>

      <!-- Campo obrigatório: Ano (limitado para um intervalo razoável) -->
      <label>Ano
        <input type="number" name="ano" min="1000" max="2100" required>
      </label>

      <!-- Campo opcional: ISBN
           - inputmode=numeric: teclado numérico em mobile
           - placeholder: exemplo visual
           - pattern/title: validação básica de formato no HTML (sem checar o dígito) -->
      <label>ISBN
        <input
          id="isbn"
          type="text"
          name="isbn"
          inputmode="numeric"
          autocomplete="off"
          spellcheck="false"
          placeholder="978-85-359-0277-5 ou 0-306-40615-2"
          pattern="^(?:97[89][- ]?\d{1,5}[- ]?\d{1,7}[- ]?\d{1,7}[- ]?\d|(?:\d[- ]?){9}[\dXx])$"
          title="Use ISBN-13 (ex.: 978-85-359-0277-5) ou ISBN-10 (ex.: 0-306-40615-2)."
        >
        <small class="muted">Aceita ISBN-10 ou ISBN-13, com ou sem hífens. O dígito final pode ser X no ISBN-10.</small>
      </label>

      <!-- Ações do formulário -->
      <button class="btn" type="submit">Salvar</button>
      <a class="btn secondary" href="/index.php">Voltar</a>
    </form>
  </div>

  <script>
    // --- Função utilitária: valida o dígito verificador de ISBN-10/13 ---
    function isValidIsbn(value) {
      // Normaliza: remove tudo que não for 0-9 ou X e deixa maiúsculo
      const s = value.toUpperCase().replace(/[^0-9X]/g, '');

      // Validação ISBN-10 (10 caracteres; último pode ser 'X' = 10)
      if (s.length === 10) {
        let sum = 0;
        for (let i = 0; i < 9; i++) {
          if (!/\d/.test(s[i])) return false;      // os 9 primeiros devem ser dígitos
          sum += (10 - i) * parseInt(s[i], 10);    // pesos 10..2
        }
        const check = (s[9] === 'X') ? 10 : (/\d/.test(s[9]) ? parseInt(s[9], 10) : -1);
        if (check < 0) return false;
        sum += check;                               // peso 1
        return sum % 11 === 0;                      // válido se múltiplo de 11
      }

      // Validação ISBN-13 (13 dígitos; pesos alternados 1 e 3)
      if (s.length === 13 && /^\d{13}$/.test(s)) {
        let sum = 0;
        for (let i = 0; i < 12; i++) {
          sum += parseInt(s[i], 10) * (i % 2 === 0 ? 1 : 3);
        }
        const check = (10 - (sum % 10)) % 10;
        return check === parseInt(s[12], 10);
      }

      // Campo vazio é permitido (ISBN é opcional)
      return s.length === 0;
    }

    // --- Máscara simples para ajudar visualmente na digitação do ISBN ---
    function formatIsbn(value) {
      // Normaliza: dígitos e 'X' apenas
      let s = value.toUpperCase().replace(/[^0-9X]/g, '');

      if (s.length <= 10) {
        // ISBN-10 (formatação aproximada 1-3-5-1, apenas estética)
        // Ex.: 0-306-40615-2
        const p1 = s.slice(0, 1);
        const p2 = s.slice(1, 4);
        const p3 = s.slice(4, 9);
        const p4 = s.slice(9);
        return [p1, p2, p3, p4].filter(Boolean).join('-');
      } else {
        // ISBN-13 (formatação aproximada 3-1-5-3-1, apenas estética)
        // Ex.: 978-85-359-0277-5
        const p1 = s.slice(0, 3);
        const p2 = s.slice(3, 4);
        const p3 = s.slice(4, 9);
        const p4 = s.slice(9, 12);
        const p5 = s.slice(12);
        return [p1, p2, p3, p4, p5].filter(Boolean).join('-');
      }
    }

    // --- Liga a máscara e a validação ao input de ISBN ---
    (function wireIsbnMask() {
      const input = document.getElementById('isbn');
      if (!input) return;

      // Define mensagem de validade customizada (HTML5 Constraint Validation)
      const setValidity = () => {
        const val = input.value.trim();
        if (val === '') { input.setCustomValidity(''); return; }  // opcional: vazio é válido
        const ok = isValidIsbn(val);
        input.setCustomValidity(ok ? '' : 'ISBN inválido. Verifique os dígitos e o formato.');
      };

      // Ao digitar: aplica máscara e revalida
      input.addEventListener('input', () => {
        const before = input.value;
        input.value = formatIsbn(before); // reescreve com hífens de ajuda visual
        setValidity();                    // atualiza a mensagem de validade
      });

      // Ao sair do campo: garante a validação atualizada
      input.addEventListener('blur', setValidity);
    })();
  </script>
</body>
</html>
