# PontoRegister

Sistema de registro de ponto eletrônico para funcionários via navegador. Uma solução moderna e eficiente para gerenciamento de horários de trabalho.

## Tecnologias Utilizadas

- **Backend:**
  - Laravel 10 (Framework PHP)
  - MySQL 8 (Banco de dados)
  - PHPUnit (Testes)

- **Frontend:**
  - Blade
  - TailwindCSS (Framework CSS)

- **DevOps:**
  - Docker
  - Docker Compose
  - Nginx


## Como Executar o Projeto

### Usando Docker (Recomendado)

1. Clone o repositório:
   ```bash
   git clone https://github.com/neiltonrodriguez/ponto-register.git
   cd ponto-register
   ```

2. Configure o arquivo .env:
   ```bash
   cp application/.env.example application/.env
   ```

3. Inicie os containers Docker(entre na pasta /docker):
   ```bash
   docker-compose up --build 
   ou 
   docker compose up --build
   ```

O build vai executar as migrations e seeds, factory e testes. A aplicação estará disponível em `http://localhost:8000`

### DADOS DE ACESSO
1 - Admin:
```bash
    - email: admin1@example.com
    - senha: admin123
```
```bash
    - email: admin2@example.com
    - senha: admin456
```
2 - Funcionário comun:
```bash
    - email: employee1@example.com
    - senha: 230053
```
```bash
    - email: employee2@example.com
    - senha: 663001
```
### Sem Docker

1. Requisitos:
   - PHP 8.1 ou superior
   - Composer
   - Node.js 16 ou superior
   - MySQL

2. Clone o repositório e acesse a pasta:
   ```bash
   git clone https://github.com/neiltonrodriguez/ponto-register.git
   cd ponto-register/application
   ```

3. Configure o ambiente:
   ```bash
   cp .env.example .env
   composer install
   php artisan key:generate
   php artisan migrate
   npm install
   npm run dev
   ```

4. Inicie o servidor:
   ```bash
   php artisan serve
   ```

A aplicação estará disponível em `http://localhost:8000`

## Executando os Testes

### Com Docker:
```bash
com docker os testes jã são executados quando sobe o aaplicação. Mas vc pode executar isolado
entre na pasta '/docker' e execute:
docker-compose exec app php artisan test tests/Unit/

```

### Sem Docker:
```bash
cd application
php artisan test tests/Unit/
```

## Recursos Disponíveis

- Registro de entrada e saída
- Histórico de registros
- Relatórios de horas trabalhadas
- Validação de CPF
- Integração com ViaCEP para endereços
- Interface responsiva e moderna

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para mais detalhes.