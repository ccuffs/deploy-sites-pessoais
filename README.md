<p align="center">
    <img width="400" height="200" src=".github/logo.png" title="Logo do projeto"><br />
    <img src="https://img.shields.io/maintenance/yes/2021?style=for-the-badge" title="Status do projeto">
    <img src="https://img.shields.io/github/workflow/status/ccuffs/template/ci.uffs.cc?label=Build&logo=github&logoColor=white&style=for-the-badge" title="Status do build">
</p>

# Sobre

Esse repositório contém os códigos e scripts utilizados para fazer deploy dos [sites pessoais](https://cc.uffs.edu.br/sites) do curso de [Ciência da Computação](https://cc.uffs.edu.br) da [Universidade Federal da Fronteira Sul](https://www.uffs.edu.br). A gerência de quais sites existem e suas propriedades (url, etc) são manuseadas no [portal de intranet do curso](https://cc.uffs.edu.br/intranet), cujo código está no repositório [ccuffs/intranet](https://github.com/ccuffs/intranet).

> ***IMPORTANTE:*** se você quer apenas utilizar os sites do curso, ex.: ter um site pessoal, veja a documentação em [cc.uffs.edu.br/sites](https://cc.uffs.edu.br/sites).

## Utilização

### 1. Pré-requisitos

Para rodar os scripts desse repositório, você precisa ter o [git](https://git-scm.com/), [php](https://php.net) e o [composer](https://getcomposer.org/) disponível na linha de comando.

### 2. Preparando tudo

Clone o repositório

```
git clone https://github.com/ccuffs/deploy-sites-pessoais & cd deploy-sites-pessoais
```

Instale as dependências

```
composer install
```

Se estiver em um ambiente Linux, certifique-se de dar permissão de execução para os arquivos `.php` na raiz do projeto:

```
chmod +x *.php
```

### 3. Rodando o script

Todas as opções do script podem ser vistas com a flag `--help`, basta rodar:

```
php deploy-sitescc.php --help
```

Se você estiver apenas testando alguma funcionalidade do script, você pode rodá-lo sem muitos problemas. Execute:

```
php deploy-sitescc.php
```

Nesse caso, o script rodará utilizando o arquivo [input-list-exemplo.json](input-list-exemplo.json) como fonte de conteúdo.
Para rodar o script em um ambiente de produção, você utilizará algo como o seguinte:

```
php deploy-sitescc.php --input-list="https://cc.uffs.edu.br/intranet/api/sites/list.json" --output-dir="/var/www/sites" --control-dir="/var/www/sites/api/status"
```

## Utilização em produção

A utilização desse repositório em produção está intrincicamente ligada ao portal de intranet do curso. Na máquina que rodará esse script (e hospedará os sites), você precisa do seguinte:

* Servidor web para servir as páginas.
* Pasta web acessível através da URL `/api/status`, por exemplo `/var/www/sites/api/status`. O caminho para essa pasta deve ser utilizado na flag `--control-dir` do script `deploy-sitescc`.

O recomendado é rodar os script de atualização de sites no cron, por exemplo, a cada 15 minutos. Para isso, rode:

```
crontab -e
```

Depois adicione a seguinte linha:

```cron
*/15 * * * * /usr/bin/php /home/fernando/www/deploy-sites-pessoais/deploy-sitescc.php --input-list="https://cc.uffs.edu.br/intranet/api/sites/list.json" --output-dir="/home/fernando/www/sites.cc.uffs.edu.br/public" --control-dir="/home/fernando/www/sites.cc.uffs.edu.br/public/api/status" >> /home/fernando/www/deploy-sites-pessoais/deploy-sitescc.log
```

Para reduzir a carga de processamento ou memória, pode-se usar `--batch-internval`, `--site-interval` e `--batch-size` ao rodar `deploy-sitescc`. Os valores default para essas flags são bem generosos.

## Contribua

Sua ajuda é muito bem-vinda, independente da forma! Confira o arquivo [CONTRIBUTING.md](CONTRIBUTING.md) para conhecer todas as formas de contribuir com o projeto. Por exemplo, [sugerir uma nova funcionalidade](https://github.com/ccuffs/deploy-sites-pessoais/issues/new?assignees=&labels=&template=feature_request.md&title=), [reportar um problema/bug](https://github.com/ccuffs/deploy-sites-pessoais/issues/new?assignees=&labels=bug&template=bug_report.md&title=), [enviar um pull request](https://github.com/ccuffs/hacktoberfest/blob/master/docs/tutorial-pull-request.md), ou simplemente utilizar o projeto e comentar sua experiência.

Veja o arquivo [ROADMAP.md](ROADMAP.md) para ter uma ideia de como o projeto deve evoluir.

## Licença

Esse projeto é licenciado nos termos da licença open-source [Apache 2.0](https://choosealicense.com/licenses/apache-2.0/) e está disponível de graça.

## Changelog

Veja todas as alterações desse projeto no arquivo [CHANGELOG.md](CHANGELOG.md).

## Projetos semelhates

Abaixo está uma lista de links interessantes e projetos similares:

* [academicpages](https://github.com/academicpages/academicpages.github.io)
* [Github Pages](https://pages.github.com)
* [HTML5 Boilerplate](https://html5boilerplate.com)
* [academic-responsive-template](https://github.com/dmsl/academic-responsive-template)
