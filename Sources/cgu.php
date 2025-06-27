<?php
include_once 'header.php';
include_once 'database/database.php';
include_once 'Processus/write_logs.php';

if(!isset($_SESSION['mail'])){
    $user = 'Anonyme';
}else{
    $stmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
    $stmt->bindParam(':mail', $_SESSION['mail']);
    $stmt->execute();
    $user = $stmt->fetchColumn();
}

$user_ip = $_SERVER['REMOTE_ADDR'];

write_logs('logs/log.txt', 'USRCGU', $user, $user_ip, 'Visite de la page "CGU"');

?>

<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/cgu.css">

<div class="page_container">

    <div class="page_title">
        <h2 id="indexhigh" class="highlighted-text h2titlemain">Conditions Générales d'Utilisation (C.G.U)</h2>
    </div>

    <hr id="main_hr">

    <div class="cgu_container">
        <section>
            <h3 class="article">
                Article 1 : Les mentions légales
            </h3>
            <p class="article_content">
                L’édition et la direction de la publication du site https://petisign.cloud est assurée par LABARBE Léo,
                domicilié 242 Rue du Faubourg Saint Antoine, 75012, Paris.
            </p>
            <p class="article_content">
                Numéro de téléphone : 07 67 43 97 82
            </p>
            <p class="article_content">
                Adresse e-mail : <a href="mailto:llabarbe@myges.fr">llabarbe@myges.fr</a>
            </p>
            <p class="article_content">
                L'hébergeur du site https://petisign.cloud est la société OVH SAS, dont le siège social est situé au 2,
                rue Kellermann, 59100 Roubaix, avec le numéro de téléphone : 1007.
            </p>
        </section>
        <section>
            <h3 class="article">
                Article 2 : Accès au site
            </h3>
            <p class="article_content">
                Le site https://petisign.cloud permet à l'Utilisateur un accès gratuit aux services suivants :
                Création et personnalisation de profil, Création et signature de pétitions, Messagerie instantanée
                entre utilisateurs, Gestion d'ami entre utilisateurs, Commentaires sur des pétitions, Système de rôles
                (Bénévole, Administrateur) et de créer son équipe, Application mobile, Moteur de recherche
                instantané, Signaler un contenu offensant (ex. un compte utilisateur, une pétition, ...), Don facultatif
                via Stripe.
            </p>
            <p class="article_content">
                Le site est accessible gratuitement en tout lieu à tout Utilisateur ayant un accès à Internet. Tous les
                frais supportés par l'Utilisateur pour accéder au service (matériel informatique, logiciels, connexion
                Internet, etc.) sont à sa charge.
            </p>
            <p class="article_content">
                L’Utilisateur non membre n'a pas accès aux services réservés. Pour cela, il doit s’inscrire en
                remplissant le formulaire. En acceptant de s’inscrire aux services réservés, l’Utilisateur membre
                s’engage à fournir des informations sincères et exactes concernant son état civil et ses coordonnées,
                notamment son adresse email.
                Pour accéder aux services, l’Utilisateur doit ensuite s'identifier à l'aide de son identifiant et de son mot
                de passe qui lui seront communiqués après son inscription.
                Tout Utilisateur membre régulièrement inscrit pourra également solliciter sa désinscription en se
                rendant à la page dédiée sur son espace personnel. Celle-ci sera effective dans un délai raisonnable.
                Tout événement dû à un cas de force majeure ayant pour conséquence un dysfonctionnement du site
                ou serveur et sous réserve de toute interruption ou modification en cas de maintenance, n'engage
                pas la responsabilité de https://petisign.cloud. Dans ces cas, l’Utilisateur accepte ainsi ne pas tenir
                rigueur à l’éditeur de toute interruption ou suspension de service, même sans préavis.
                L'Utilisateur a la possibilité de contacter le site par messagerie électronique à l’adresse email de
                l’éditeur communiqué à l’ARTICLE 1.
            </p>
        </section>
        <section>
            <h3 class="article">
                Article 3 : Collecte des données
            </h3>
            <p class="article_content">
                Le site assure à l'Utilisateur une collecte et un traitement d'informations personnelles dans le respect
                de la vie privée conformément à la loi n°78-17 du 6 janvier 1978 relative à l'informatique, aux fichiers
                et aux libertés.
            </p>
            <p class="article_content">
                En vertu de la loi Informatique et Libertés, en date du 6 janvier 1978, l'Utilisateur dispose d'un droit
                d'accès, de rectification, de suppression et d'opposition de ses données personnelles. L'Utilisateur
                exerce ce droit : 
            </p>
            <ul>
                <li>par mail à l'adresse email : llabarbe@myges.fr</li>
                <li>via un formulaire de contact</li>
                <li>via son espace personnel</li>
            </ul>
        </section>
        <section>
            <h3 class="article">
                Article 4 : Propriété intellectuelle
            </h3>
            <p class="article_content">
                Les marques, logos, signes ainsi que tous les contenus du site (textes, images, son…) font l'objet
                d'une protection par le Code de la propriété intellectuelle et plus particulièrement par le droit d'auteur.
            </p>
            <p class="article_content">
                L'Utilisateur doit solliciter l'autorisation préalable du site pour toute reproduction, publication, copie
                des différents contenus. Il s'engage à une utilisation des contenus du site dans un cadre strictement
                privé, toute utilisation à des fins commerciales et publicitaires est strictement interdite.
            </p>
            <p class="article_content">
                Toute représentation totale ou partielle de ce site par quelque procédé que ce soit, sans l’autorisation
                expresse de l’exploitant du site Internet constituerait une contrefaçon sanctionnée par l’article L 335-2
                et suivants du Code de la propriété intellectuelle.
                Il est rappelé conformément à l’article L122-5 du Code de propriété intellectuelle que l’Utilisateur qui
                reproduit, copie ou publie le contenu protégé doit citer l’auteur et sa source.
            </p>
        </section>
        <section>
            <h3 class="article">
                Article 5 : Responsabilité
            </h3>
            <p class="article_content">
                Les sources des informations diffusées sur le site https://petisign.cloud sont réputées fiables mais le
                site ne garantit pas qu’il soit exempt de défauts, d’erreurs ou d’omissions.
            </p>
            <p class="article_content">
                Les informations communiquées sont présentées à titre indicatif et général sans valeur contractuelle.
                Malgré des mises à jour régulières, le site https://petisign.cloud ne peut être tenu responsable de la
                modification des dispositions administratives et juridiques survenant après la publication. De même, le
                site ne peut être tenue responsable de l’utilisation et de l’interprétation de l’information contenue dans
                ce site. <br>
                L'Utilisateur s'assure de garder son mot de passe secret. Toute divulgation du mot de passe, quelle
                que soit sa forme, est interdite. Il assume les risques liés à l'utilisation de son identifiant et mot de
                passe. Le site décline toute responsabilité.
                Le site https://petisign.cloud ne peut être tenu pour responsable d’éventuels virus qui pourraient
                infecter l’ordinateur ou tout matériel informatique de l’Internaute, suite à une utilisation, à l’accès, ou
                au téléchargement provenant de ce site. 
            </p>
            <p class="article_content">
                La responsabilité du site ne peut être engagée en cas de force majeure ou du fait imprévisible et
                insurmontable d'un tiers.
            </p>
        </section>
        <section>
            <h3 class="article">
                Article 6 : Liens hypertextes
            </h3>
            <p class="article_content">
                Des liens hypertextes peuvent être présents sur le site. L’Utilisateur est informé qu’en cliquant sur ces
                liens, il sortira du site https://petisign.cloud. Ce dernier n’a pas de contrôle sur les pages web sur
                lesquelles aboutissent ces liens et ne saurait, en aucun cas, être responsable de leur contenu.
            </p>
        </section>
        <section>
            <h3 class="article">
                Article 7 : Cookies
            </h3>
            <p class="article_content">
                L’Utilisateur est informé que lors de ses visites sur le site, un cookie peut s’installer automatiquement
                sur son logiciel de navigation.
            </p>
            <p class="article_content">
                Les cookies sont de petits fichiers stockés temporairement sur le disque dur de l’ordinateur de
                l’Utilisateur par votre navigateur et qui sont nécessaires à l’utilisation du site https://petisign.cloud. Les
                cookies ne contiennent pas d’information personnelle et ne peuvent pas être utilisés pour identifier
                quelqu’un. Un cookie contient un identifiant unique, généré aléatoirement et donc anonyme. Certains
                cookies expirent à la fin de la visite de l’Utilisateur, d’autres restent.
            </p>
            <p class="article_content">
                L’information contenue dans les cookies est utilisée pour améliorer le site https://petisign.cloud.
                En naviguant sur le site, L’Utilisateur les accepte.
                3
                L’Utilisateur doit toutefois donner son consentement quant à l’utilisation de certains cookies.
                A défaut d’acceptation, l’Utilisateur est informé que certaines fonctionnalités ou pages risquent de lui
                être refusées.
                L’Utilisateur pourra désactiver ces cookies par l’intermédiaire des paramètres figurant au sein de son
                logiciel de navigation.
            </p>
        </section>
        <section>
            <h3 class="article">
                Article 8 : Publication par l’Utilisateur
            </h3>
            <p class="article_content">
                Le site permet aux membres de publier les contenus suivants :
                Commentaires, Pétitions, Messages.
            </p>
            <p class="article_content">
                Dans ses publications, le membre s’engage à respecter les règles de la Netiquette (règles de bonne
                conduite de l’internet) et les règles de droit en vigueur.
            </p>
            <p class="article_content">
                Le site peut exercer une modération sur les publications et se réserve le droit de refuser leur mise en
                ligne, sans avoir à s’en justifier auprès du membre.
            </p>
            <p class="article_content">
                Le membre reste titulaire de l’intégralité de ses droits de propriété intellectuelle. Mais en publiant une
                publication sur le site, il cède à la société éditrice le droit non exclusif et gratuit de représenter,
                reproduire, adapter, modifier, diffuser et distribuer sa publication, directement ou par un tiers autorisé,
                dans le monde entier, sur tout support (numérique ou physique), pour la durée de la propriété
                intellectuelle. Le Membre cède notamment le droit d'utiliser sa publication sur internet et sur les
                réseaux de téléphonie mobile.
            </p>
            <p class="article_content">
                La société éditrice s'engage à faire figurer le nom du membre à proximité de chaque utilisation de sa
                publication.
            </p>
            <p class="article_content">
                Tout contenu mis en ligne par l'Utilisateur est de sa seule responsabilité. L'Utilisateur s'engage à ne
                pas mettre en ligne de contenus pouvant porter atteinte aux intérêts de tierces personnes. Tout
                recours en justice engagé par un tiers lésé contre le site sera pris en charge par l'Utilisateur.
            </p>
            <p class="article_content">
                Le contenu de l'Utilisateur peut être à tout moment et pour n'importe quelle raison supprimé ou
                modifié par le site, sans préavis.
            </p>
        </section>
        <section>
            <h3 class="article">
                Article 9 : Droit applicable et juridiction compétente
            </h3>
            <p class="article_content">
                La législation française s'applique au présent contrat. En cas d'absence de résolution amiable d'un
                litige né entre les parties, les tribunaux français seront seuls compétents pour en connaître.
                Pour toute question relative à l’application des présentes CGU, vous pouvez joindre l’éditeur aux
                coordonnées inscrites à l’ARTICLE 1.
            </p>
        </section>
        <hr  id="cgu_hr">
        <button class="custom-button cgu"><img src="/Resources/img/ui_icons/download.png" alt="">&nbsp;&nbsp;Télécharger la version PDF</button>
    </div>
</div>

<script>

// download pdf file in new tab
document.querySelector('.cgu').addEventListener('click', function() {
    window.open('/Resources/CGU.pdf', '_blank');
});

</script>

<script src="js/trancho_popup.js"></script>
<script src="js/trancho_elements_logic.js"></script>
<script src="js/trancho_detection.js"></script>

<?php
include_once 'footer.php'
?>
