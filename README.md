GeoNames API Example
=================================================
[![Build Status](https://semaphoreci.com/api/v1/mariojrrc/geonames-api/branches/master/badge.svg)](https://semaphoreci.com/mariojrrc/geonames-api)

Este projeto contém uma API REST de exemplo escrita em PHP utilizando Zend Apigility e Mongo DB.
Possui basicamente dois endpoints com CRUD:

- /cities
- /states

Para acessar a api é necessário ter tokens mapeados
no arquivo token-config.php localizado na pasta data.

A documentação dos endpoints é feita utilizando
a notação do ApiBluePrint e é gerada utilizando o Aglio.

## Instalação Aglio
Instale globalmente via NPM. É necessário ter o Node.js instalado.

```bash
npm install -g aglio aglio-theme-olio
```

Depois, gere o HTML com layout do Olio.

```bash
aglio -i doc/geonames.apib -o doc/geonames.html
```

* [mais informações sobre o ApiBluePrint](https://apiblueprint.org/)