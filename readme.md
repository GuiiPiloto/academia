README – TOPFIT Sistema de Gerenciamento de Academia

Projeto desenvolvido por Guilherme Piloto e Andrey Ribeiro, alunos do curso de Engenharia de Software da UniBrasil, em Curitiba/PR.

📋 Descrição do Projeto
O TOPFIT é um sistema completo de gerenciamento de academia, desenvolvido em PHP com MySQL e CSS puro, sem frameworks, focado em um visual moderno, responsivo e com suporte a tema claro/escuro.
O sistema possui login por tipo de usuário e funcionalidades específicas para admin, professor e aluno, oferecendo recursos como:

- Controle de fichas de treino
- Avaliações físicas
- Registro de presença
- Sistema de mensagens
- Painel de estatísticas e administração

🚀 Como Rodar o Projeto
Pré-requisitos:
- XAMPP instalado
- PHP 7.4 ou superior
- MySQL habilitado no XAMPP
- Editor de código (ex: VS Code)

Passo a Passo:
1. Clone o repositório:
   git clone https://github.com/GuiiPiloto/academia.git

2. Inicie o XAMPP e ative Apache e MySQL.

3. Importe o banco de dados:
   - Abra o phpMyAdmin
   - Crie o banco com nome 'academia'
   - Importe o arquivo BD.sql incluído no projeto

4. Acesse o sistema:
   - Navegue até http://localhost/academia no navegador
   - Faça login com os dados cadastrados ou use a tela pública de cadastro de aluno

👥 Divisão de Equipe
Integrante	Função no Projeto
Guilherme Piloto	Programação Backend, Banco de Dados e Integração
Andrey Ribeiro	Design das Telas, Lógica de Frontend e Documentação

🛠️ Tecnologias Utilizadas
Linguagens e Ferramentas:
- PHP (sem frameworks)
- MySQL (phpMyAdmin)
- CSS Puro (com temas claro/escuro)
- HTML5
- Ngrok (para exposição pública local)
- XAMPP (ambiente local)

Arquitetura:
- Baseada em separação de pastas por tipo de usuário
- Sistema modular e escalável, com foco em organização e segurança
- Utilização de includes para cabeçalho, rodapé e verificação de sessão

Banco de Dados:
- usuarios (alunos, professores, admins)
- avaliacoes_fisicas
- fichas_treino
- presencas
- mensagens

📦 Funcionalidades por Tipo de Usuário
Aluno:
- Visualizar ficha de treino
- Registrar presença
- Visualizar e editar avaliações físicas
- Trocar mensagens com o professor

Professor:
- Criar fichas de treino por aluno
- Realizar avaliações físicas
- Trocar mensagens com alunos

Admin:
- Dashboard com resumo
- Gerenciamento de usuários (CRUD)
- Acesso exclusivo ao painel administrativo

🌐 Acesso Externo (Ngrok)
Para acessar o sistema remotamente:
1. Instale o Ngrok: https://ngrok.com/
2. No terminal, execute: ngrok http 80
3. Compartilhe o link gerado (ex: https://abcd-1234.ngrok.io)

📎 Licença
Este projeto é de uso acadêmico, desenvolvido para fins educacionais na UniBrasil.

🔗 Repositório
https://github.com/GuiiPiloto/academia.git
