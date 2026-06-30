# 🚀 SimpleIntranet - Social Intranet Platform

## Uma Intranet Social Simples com Estilo Hacker/Terminal

---

## 📋 Sobre o Projeto

**SimpleIntranet** é uma plataforma de intranet social desenvolvida em PHP puro, com uma interface moderna e futurista inspirada em terminais e ambientes hacker. O projeto foi criado para facilitar a comunicação interna, compartilhamento de conhecimento e colaboração entre equipes, com um visual único e recursos completos.

### 🎯 Propósito

- **Comunicação interna** centralizada e eficiente
- **Compartilhamento de conhecimento** através de posts formatados em Markdown
- **Gestão de usuários** com sistema de permissões (admin/user) baseado no ip apenas
- **Interface moderna** com tema claro/escuro e estilo técnico

---

## ✨ Funcionalidades

### 📝 Sistema de Posts
- ✅ Criar, editar e excluir posts
- ✅ Suporte completo a **Markdown** com renderização em tempo real
- ✅ Barra de ferramentas com atalhos para formatação (negrito, itálico, cabeçalhos, listas)
- ✅ **Blocos de código** com suporte a 12+ linguagens (Bash, PHP, JavaScript, Python, etc.)
- ✅ Pré-visualização ao vivo do conteúdo
- ✅ Busca em tempo real por título, conteúdo ou autor
- ✅ Paginação com configuração de itens por página (5, 10, 20, 50)

### 👥 Gestão de Usuários
- ✅ Sistema de autenticação por IP
- ✅ Perfis de usuário com avatar, nome, email, departamento e bio
- ✅ Sistema de permissões (Admin/User)
- ✅ Lista de conexões com status online
- ✅ Primeiro usuário configuração automática como admin
- ✅ Apenas separa posts por máquina, deduzindo que se trata de pessoa única

### 🎨 Interface
- ✅ **Tema claro/escuro** com persistência via localStorage
- ✅ Design inspirado em terminal/hacker
- ✅ Animações suaves e feedback visual
- ✅ Layout responsivo para todos os dispositivos
- ✅ Grid tecnológico animado no fundo
- ✅ Fonte JetBrains Mono para código e Inter para texto

### 🔧 Recursos Técnicos
- ✅ **Markdown avançado** com destaque de código
- ✅ Editor com atalhos de teclado (Ctrl+B, Ctrl+I, Ctrl+K, Tab)
- ✅ Barra de ferramentas completa no editor
- ✅ Sistema de paginação com navegação por números
- ✅ Busca com debounce e destaque dos termos
- ✅ Loading states e feedback ao usuário

---

## 🛠️ Tecnologias Utilizadas

### Backend
- **PHP 7.4+** - API RESTful
- **JSON** - Armazenamento de dados (sem necessidade de banco de dados)
- **Markdown** - Renderização de conteúdo

### Frontend
- **HTML5 + CSS3** - Estrutura e estilos
- **JavaScript Vanilla** - Interatividade e funcionalidades
- **Bootstrap 5** - Layout e componentes responsivos
- **Bootstrap Icons** - Ícones
- **Marked.js** - Renderização de Markdown

### Fontes
- **JetBrains Mono** - Código e elementos técnicos
- **Inter** - Texto e UI geral

---

## 📁 Estrutura do Projeto

```
intranet/
├── _intra_posts_json/          # Armazenamento dos posts (JSON)
├── _intra_users_json/          # Armazenamento dos usuários (JSON)
├── editor-modal.php            # Modal do editor de posts
├── editor.js                   # Lógica principal do editor e posts
├── delete-post.php             # API: excluir post
├── delete-user.php             # API: excluir usuário
├── edit-post.php               # API: editar post
├── edit-user.php               # API: editar usuário
├── functions.php               # Funções auxiliares
├── home_sidebar_center.php     # Coluna central (posts)
├── home_sidebar_left.php       # Coluna esquerda (servidores, arquivos)
├── home_sidebar_right.php      # Coluna direita (status, geradores)
├── index.php                   # Página principal
├── listfiles.php               # Listagem de arquivos do sistema
├── list-posts.php              # API: listar posts
├── list-users.php              # API: listar usuários
├── save-post.php               # API: salvar post
├── save-user.php               # API: salvar usuário
├── script.js                   # Scripts globais
├── style.css                   # Estilos principais (organizado)
├── test.php                    # Script de teste de ambiente
├── theme.php                   # Template principal
├── user-config.php             # Configurações de usuário
├── user-manager.js             # Gerenciamento de usuários
└── user-manager.php            # Página de gerenciamento
```

---

## 🚀 Instalação

### Pré-requisitos
- PHP 7.4 ou superior
- Servidor web (Apache/Nginx) ou PHP Built-in Server
- Navegador moderno (Chrome, Firefox, Edge, Safari)

### Passos

1. **Clone o repositório**
```bash
git clone https://github.com/seu-usuario/intranet.git
cd intranet
```

2. **Configure o servidor web**
   - Mapeie o diretório `intranet/` como document root
   - Ou use o servidor embutido do PHP:
```bash
php -S localhost:8000
```

3. **Permissões de diretório**
```bash
chmod 755 _intra_posts_json _intra_users_json
```

4. **Acesse no navegador**
```
http://localhost:8000
```

5. **Configuração inicial**
   - O sistema detecta automaticamente se não há usuários
   - O primeiro usuário criado será o administrador do sistema
   - Clique em "Gerenciar Usuários" ou siga o banner de configuração

---

## 📖 Como Usar

### Criar um Post
1. Clique em **"Criar Novo Post"**
2. Preencha o título e o conteúdo
3. Use a barra de ferramentas para formatar (Markdown)
4. Clique em **"Salvar Post"** ou use `Ctrl+Enter`

### Gerenciar Usuários
1. Acesse a página de gerenciamento
2. Clique em **"Novo Usuário"** para adicionar
3. Preencha os dados (nome, email, IP)
4. Defina o papel (Admin/User)

### Buscar Posts
1. Digite na barra de busca
2. A busca é feita em título, conteúdo e autor
3. Use `Ctrl+F` para focar na busca rapidamente

### Navegar Posts
1. Use os números de página no rodapé
2. Configure quantos posts por página (5, 10, 20, 50)
3. Posts são ordenados do mais recente para o mais antigo

---

## 🎨 Customização

### Cores e Temas
- As variáveis CSS estão no início do `style.css`
- Tema escuro é o padrão, tema claro é suportado
- A cor primária pode ser alterada (`--accent-primary`)

### Adicionar Linguagens de Código
- Edite o dropdown no `editor-modal.php`
- Adicione uma nova opção no menu de blocos de código

### Servidores e Links
- Edite o array `$servers` no `home_sidebar_left.php`

---

## 🤝 Contribuição

Contribuições são bem-vindas! Siga estes passos:

1. **Fork** o projeto
2. Crie uma **branch** para sua feature (`git checkout -b feature/AmazingFeature`)
3. **Commit** suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. **Push** para a branch (`git push origin feature/AmazingFeature`)
5. Abra um **Pull Request**

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

---

## 👨‍💻 Autor

**Crocciari** - [GitHub](https://github.com/crocciari)

---

## 🙏 Agradecimentos

- [Bootstrap](https://getbootstrap.com) - Framework CSS
- [Marked.js](https://marked.js.org) - Renderização de Markdown
- [JetBrains](https://www.jetbrains.com) - Fonte JetBrains Mono
- [Google Fonts](https://fonts.google.com) - Fonte Inter

---

## 📊 Status do Projeto

![Status](https://img.shields.io/badge/status-ativo-brightgreen)
![Versão](https://img.shields.io/badge/versão-1.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4)
![Licença](https://img.shields.io/badge/licença-MIT-green)

---

## 🔮 Próximas Funcionalidades

- [ ] Sistema de notificações em tempo real
- [ ] Comentários nos posts
- [ ] Upload de imagens e arquivos
- [ ] Sistema de likes/reactions
- [ ] Chat interno em tempo real
- [ ] Pesquisa avançada com filtros
- [ ] Exportação de posts
- [ ] API externa para integrações

---

## 📞 Suporte

Para suporte, abra uma issue no GitHub.

---

**Feito com ❤️ e muito café ☕**