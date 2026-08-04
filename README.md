# kirby-plugin-annotator
Een Kirby CMS-plugin voor het toevoegen en beheren van annotaties binnen je website. Met deze plugin kunnen annotaties worden gekoppeld aan een afbeelding zodat extra context, opmerkingen of metadata zichtbaar gemaakt worden en dat er een verhaal verteld kan worden over de afbeelding.

---

<br/>

## ✨ Features
- **Kirby field**: Voeg dit als veld toe aan je blueprint.
- **Kirby content blok**: Voeg dit toe al vrije content in een layout of blocks field op een pagina
- **Flexibele layouts**: Maak meerdere layout opties of maak meerdere metadata velden per annotatie

<br/>

## ⚠️ Requirements
- Kirby 5+
- PHP >= 8.3

<br/>

## Instalatie
### Docker
Laat de plugin in je docker image draaien en zet enkel de pagina's die je wilt aanpassen of bijcreëren (zie project structuur) in je project zelf.
```php
ARG KIRBY_PLUGIN_ANNOTATOR_BRANCH=main
RUN wget --no-verbose "https://github.com/libis/kirby-plugin-annotator/archive/${KIRBY_PLUGIN_ANNOTATOR_BRANCH}.zip" -O /var/www/html/site/plugins/annotator.zip \
    && unzip -q /var/www/html/site/plugins/annotator.zip -d /var/www/html/site/plugins/ \
    && rm /var/www/html/site/plugins/annotator.zip \
    && mv /var/www/html/site/plugins/kirby-plugin-annotator-${KIRBY_PLUGIN_ANNOTATOR_BRANCH} /var/www/html/site/plugins/annotator \
    && chown -R www-data:www-data /var/www/html/site/plugins/annotator
```
<br/>

### Manually
1. Download of clone deze repository.
2. Plaats de plugin in de map:

<br/>

### Stijl & functionaliteiten
#### Stijl
Deze plugin komt met een standaard styling deze kan je aanpassen door de classes in je iegen css op te roepen en aan te passen. Gebruik je tailwind voeg deze file dan niet toe en kopieer alle snippet files naar je project in `site/snippets` en voeg de classes toe.
Voeg dit in je head toe:
```
<link rel="stylesheet" href="/media/plugins/libis/annotator/css/index.css">
```

<br/>

#### Functionaliteiten
Voeg volgende JS file toe in je footer (zonder deze file zal de plugin niet werken):
```
<script type="module" src='/media/plugins/libis/annotator/js/main.js'></script>
```

<br/>

## Gebruik
In layout of block fields:
``` yml
- annotator
```
<br/>

In je blueprint als field:
``` yml
annotator:
  type: annotator
  label: libis.annotator.annotator
  infoTemplate: imageTitleText
  query: page('media-folder').children.files
  limit: 8
  template:
    type: select
    label: libis.annotator.template
    options: 
      twocolumn: libis.annotator.template.twocolumn
      onecolumn: libis.annotator.template.onecolumn
  fields:
    title:
      label: libis.annotator.title
      type: text
      mobile: true
    text:
      label: libis.annotator.text
      type: textarea
    image:
      label: libis.annotator.image
      type: annotatorFile
      query: page('media-folder').children.files
      limit: 8
```
Als je extra layout (templates) opties wilt voorzien kan dit bij template. Als je extra velden aan een punt wilt toevoegen kan dit bij fields.

<br/>

## Customalisatie
Je kan meerdere layout (templates) opties creëeren en ook meerdere fields toevoegen aan een annotatiepunt. Let op: je moet hier wel enkele dingen voor aanpassen. Ook bepaald de infoTemplate welke file hij zal renderen voor de velden te tonen.

Blueprint:
- **layout/blocks field**: [Kopieer de inhoud van](https://github.com/libis/kirby-plugin-annotator/blob/main/blueprints/blocks/annotator.yml) en creëer een annotator.yml file in blueprints/blocks. Pas de inhoud naar keuze aan.
- **als field op een pagina**: pas je blueprint aan. Voeg de extra velden of template opties toe.

<br/>

Files:
- Extra template: creeër in `site/snippets` een folder annotatorTemplates en creëer een file met de exacte naam van je veld optie (bv.: onecolumn); Kijk naar de bestaande voorbeelden voorde juiste structuur.
- Extra infoTemplate (door aanpassing aan velden):  creeër in `site/snippets` een folder infoTemplates en creëer een file met de exacte naam van je infoTemplate (bv.: textTitle); Kijk naar de bestaande voorbeelden voorde juiste structuur.
  
<br/>
