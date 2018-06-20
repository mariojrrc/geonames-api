GeoNames API Example
=================================================
[![Build Status](https://semaphoreci.com/api/v1/mariojrrc/geonames-api/branches/master/badge.svg)](https://semaphoreci.com/mariojrrc/geonames-api)

Este projeto contém uma API REST de exemplo escrita em PHP utilizando Zend Apigility e Mongo DB.
Possui basicamente dois endpoints com CRUD:

- /cities
- /states

Para acessar a api é necessário ter tokens mapeados
no arquivo token-config.php localizado na pasta `data`.

A documentação dos endpoints deve ser lida e pode ser encontrada na pasta `doc`. Ela é feita utilizando
a notação do ApiBluePrint e é gerada utilizando o Aglio. Também existe a documentação convertida para OpenAPI v3.

## Executando o projeto

1. Copie ou renomeie o arquivo `config/autoload/doctrine-mongo-odm.local.php.dist`
2. Execute `docker-compose up`
3. Em seguida execute `docker exec -it geonames_apigility composer install`
4. Faça os testes via [Postman](https://www.getpostman.com/) ou similar no endereço `0.0.0.0:8080/cities`
5. Caso não tenha o docker é necessário verificar as dependências do projeto e ajustar as configurações do mongo no arquivo acima

## EXTRA - Instalação Aglio
Instale globalmente via NPM. É necessário ter o Node.js instalado.

```bash
npm install -g aglio aglio-theme-olio
```

Depois, gere o HTML com layout do Olio.

```bash
aglio -i doc/geonames.apib -o doc/geonames.html
```

* [mais informações sobre o ApiBluePrint](https://apiblueprint.org/)