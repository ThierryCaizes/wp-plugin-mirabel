# plugin-mirabel
<h2>Notice de développement d'une extension WordPress pour l'API Mirabel</h2>

Cette notice n'est qu'une base de récupération de l'API de Mirabel pour pouvoir développer une extension WordPress sur-mesure et de l'adapter en fonction de vos besoins.<br><br>
<strong>Lien de l'API</strong>: https://reseau-mirabel.info/api

<h3>Objectifs</h3>
<h4>1-Custom Post Type</h4>
L'objectif est de récupérer les revues sous forme de post. Un post = une revue.<br>
Pour cela vous devez créer un type de publication <i>(voir mirabel-cpt.php)</i>. <br>
Cela vous permettra de créer une rubrique spécifique <i>(Par exemple : Catalogue)</i> dans le tableau de Bord de WordPress.
<br>
Lors de l'appelle de l'API, les posts se créeront automatiquement.
<br>
<h4>2-Metadonnées</h4>
Vous devrez insérer des champs personnalisées dans chacun des posts.<br>
Ces champs vous permettrons d'importer les types de données souhaités.<br>
Ce fichier ce trouve dans import.php <br>
<br>
Dans notre exemple l'import des données se fait en trois grandes phases :
<ol>
  <li>L'import des données globales</li>
  <ul>
    <li>Titre</li>
    <li>ID de la revue <i>(Important pour éviter les doublons)</i></li>
    <li>ISSN</li><i>(voir "Cas spécifiques")</i>
    <li>Périodicité</li>
    <li>Langues <i>(voir "Cas spécifiques")</i></li> 
    <li>URLS</li>
    <li>Revue Obsolete <i>(voir "Cas spécifiques")</i></li>
  </ul>
  <li>L'import des thématiques</li>
  <li>L'import des ressources</li>
</ol>
<h2 id="cas">Cas spécifiques</h2>
<h3>ISSNS</h3>
Les ISSNS se distinguent en deux types d'objets :
<ul>
  <li>ISSN Electronique</li>
  <li>ISSN Papier</li>
</ul>
S'il y a les deux types d'ISSN pour une revue l'ordre des objets peut toujours varier entre deux revues.
<h3>Revues Obsoletes</h3>
Nativement lors de l'import des revues, l'API récupère toute les revues d'un seul même ID (Revue actuel et passé). <br><br>
Dans notre exemple nous avons souhaité d'importer la revue actuellement publié.<br>
Pour cela une clé de tableau est indiqué 'Obsoletepar' dans le json de la grappe concerné<br>(exemple: https://reseau-mirabel.info/api/titres?grappeid=2) :<br><br>
<ul>
  <li>Obsoletepar: Null -> indique une revue actuellement publié</li>
  <li>Obsoletepar: ID -> indique une revue passé</li>
</ul>
<h3>Langues</h3>
Nativement les langues sont importées de cette manière (fre, eng, ger etc....).<br>
Pour que cela soit 100% accessible pour les utilisateurs vous serez obligé de faire un mapping <i>(Voir langues-mapping.php)</i>.
