cdn-app/
├── app/
│   ├── core/              # Helpers e serviços core
│   └── router/            # Rotas da API
│       ├── api/
│       │   └── v1/
│       │       ├── upload.php
│       │       ├── delete.php
│       │       ├── info.php
│       │       └── resize.php
│       └── 404.php
│
├── public/                # Domínio cdn.omeu.space (API)
│   └── index.php
│
├── storage/               # Domínio cdn.tudocomlizzyman.com
│   ├── u/                 # Arquivos dos usuários
│   └── cache/             # Cache de imagens redimensionadas
│
├── src/
│   ├── Controllers/       # Controllers da aplicação
│   ├── Entities/          # Cycle ORM Entities
│   ├── Services/          # Serviços (Storage, Resize, etc.)
│   └── Middleware/        # Middleware personalizados
│
├── config/
│   └── database.php       # Configuração do Cycle ORM
│
├── .env
├── .env.example
├── README.md
├── TODO.md
└── composer.json