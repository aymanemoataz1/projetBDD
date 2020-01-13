<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="includes/icon.ico">

    <title>Projet BDD</title>

    <!-- Bootstrap core CSS -->
    <link href="includes/bootstrap.min.css" rel="stylesheet">


    <!-- Custom styles for this template -->
    <link href="includes/stylesheet.css" rel="stylesheet">
    <link href="includes/cover.css" rel="stylesheet">
    <link href="includes/offcanvas.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<script src="includes/jquery-3.3.1.slim.min.js" ></script>
<script src="includes/popper.min.js" ></script>
<nav class="navbar navbar-fixed-top navbar-inverse">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">

                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="navbar-brand" >Projet BDD</div>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="index.php">Home</a></li>
                <li><a href="remplir.php">Remplir</a></li>
                <li class="active"><a href="Requetes.php">Requêtes</a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="display.php" id="navbardrop" data-toggle="dropdown">
                        Tables
                        <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-center">
                        <a class="text-muted btn btn-dark text-center" href="display.php?bdd=Service">Service</a>
                        <div class="dropdown-divider"></div>
                        <a class="text-muted btn btn-dark text-center" href="display.php?bdd=Salle">Salle</a>
                        <div class="dropdown-divider"></div>
                        <a class="text-muted btn btn-dark text-center" href="display.php?bdd=Infirmier">Infirmier</a>
                        <div class="dropdown-divider"></div>
                        <a class="text-muted btn btn-dark text-center" href="display.php?bdd=Patient">Patient</a>
                        <div class="dropdown-divider"></div>
                        <a class="text-muted btn btn-dark text-center" href="display.php?bdd=Medecin">Medecin</a>
                        <div class="dropdown-divider"></div>
                        <a class="text-muted btn btn-dark text-center" href="display.php?bdd=Hospitalisation">Hospitalisation</a>
                        <div class="dropdown-divider"></div>
                        <a class="text-muted btn btn-dark text-center" href="display.php?bdd=Repartition">Acte</a>
                    </div>
                </li>
            </ul>
        </div><!-- /.nav-collapse -->
    </div><!-- /.container -->
</nav><!-- /.navbar -->

<div class="container">

    <div class="row row-offcanvas row-offcanvas-right">

        <div class="jumbotron"><h2 class="cover-heading text-muted">Interrogation de la base</h2></div>

        <?php
        include_once 'includes/header.php';
        $requetes = array('SELECT Nom, Prenom FROM PATIENT P, HOSPITALISATION H WHERE P.NumPat=H.NumPat AND DateEntree="03/07/2018";',
            'SELECT M.Nom FROM Medecin M, Service S WHERE M.NumMed=S.NumMed AND M.Specialite="Cancerologue";',
            'SELECT Serv.NumService,SUM(Nblits) FROM SALLE S, SERVICE Serv WHERE S.NumService=Serv.NumService GROUP BY S.NumService;',
            'SELECT S.NumSalle, Nblits - COUNT(NumPat) as Disponible FROM SALLE S, SERVICE Serv, HOSPITALISATION H WHERE S.NumService=Serv.NumService AND H.NumService=S.NumService AND Serv.Nom="Cardiologie" AND DateEntree="12/05/2018" GROUP BY S.NumSalle, Nblits;',
            'SELECT Nom FROM PATIENT WHERE NumPat NOT IN (SELECT NumPat FROM ACTE A, Medecin M WHERE A.numMed=M.numMed AND Specialite="Cardiologue");',
            'SELECT Nom from Patient p WHERE NOT EXISTS(SELECT * From Service S WHERE NOT EXISTS( SELECT * FROM Acte A WHERE A.NumPat=p.NumPat AND A.NumService=S.NumService));',
            'SELECT M.Nom, M.Specialite, A.NumPat FROM Medecin M, Acte A WHERE A.NumPat=(SELECT NumPat from Patient p WHERE NOT EXISTS(Select * From Service S WHERE NOT EXISTS(Select * FROM Acte A WHERE A.NumPat=p.NumPat AND A.NumService=S.numService))) AND A.NumMed=M.NumMed GROUP BY M.NumMed;',
            'SELECT Nom, Prenom FROM PATIENT P WHERE P.NumPat IN (SELECT NumPat FROM HOSPITALISATION) AND NOT P.NumPat IN (SELECT NumPat FROM HOSPITALISATION WHERE DateSortie - DateEntree <= 14);',
            'SELECT DISTINCT P.Nom FROM Acte A, Patient P WHERE A.NumPat IN (SELECT NumPat FROM acte) AND NOT A.NumPat IN (SELECT NumPat FROM HOSPITALISATION) AND A.NumPat=P.NumPat;', 
            'SELECT DISTINCT A.NumService FROM Acte A WHERE A.NumService IN (SELECT NumService FROM acte) AND NOT A.NumService IN (SELECT NumService FROM HOSPITALISATION);',
            'SELECT NumPat, NumService FROM Acte GROUP BY NumPat HAVING COUNT(NumService)=1 ;',
            'SELECT NumService FROM Hospitalisation WHERE DATEDIFF(DateSortie,DateEntree);',
            'SELECT * FROM Patient WHERE Patient.NumPat in (SELECT NumPat FROM Hospitalisation WHERE DateSortie-Date(IFNull(DateEntree,CURDATE())) > 3) AND Patient.Mutuelle <> "MUT";',
            'SELECT AVG(C) as average FROM Medecin JOIN (SELECT NumMed,COUNT(DISTINCT NumPat) AS C FROM acte GROUP BY NumMed) A ON Medecin.NumMed=A.NumMed;',
            'SELECT AVG(C) as average FROM (SELECT DateActe,COUNT(*) as C FROM Acte GROUP BY DateActe) A;');
            
        $texte = array( '1. Quels sont les patients entrés à une date que l’on saisit ?',
        '2. Quels sont les cancérologues qui sont chefs de service ?',
        '3. Quel est le nombre de lits dans chaque service ?',
        '4. Quel est le nombre de lits libres dans chaque salle du service de cardiologie au 04/07/2018 ?',
        '5. Quels sont les patients qui n\ ’ont jamais été traités par un cardiologue ?',
        '6. Quels sont les patients qui ont subi au moins un acte dans tous les
        services ?',
        '7. Quels sont les médecins, leur spécialité et le nom du patient, qui ont traités un patient qui a subit un acte 
        dans tous les services de l’hopital ?trierer le résultat par médecin.',
        '8. Quel sont les patients qui sont toujours restés plus de deux semaines lors de leurs hospitalisations ?',
        '9. Quels sont les patients qui ont toujours été traités sans être hospitalisés ?',
        '10. Quels sont les services qui n\ ’ont traités que des patients non hospitalisés ?',
        '11. Quels sont les patients et le service, des patients qui n\’ont eu un acte que dans un seul service ?',
        '12. Quelles sont les services dont la majorité des patients ont été hospitalisés au moins 2 jours ?',
        '13. Quels sont les patients hospitalisés plus de trois jours qui ne sont pas à la mutelle MUT ?',
        '14. Quel est le Nombre moyen de patients (différents) par médecin (patient ayant subit un acte par le médecin ?',
        '15. Quelle est la moyenne des actes par jour pour l’ensemble des medecins ?' ,);

        $conn->query('USE Hopital;')  or die($conn->error);

        for ($i = 1; $i <= count($texte);$i++){
            echo '<h2><u>Requête n°'.$i.':</u></h2>';
            echo '<p><h4>'.$texte[$i-1].'</h4></p>';
            echo '<a class="btn btn-info" href="Requetes.php?request='.$i.'#tableau">Executer cette requête.</a>';
        };
        if(isset($_GET['request'])){
            $result = $conn->query($requetes[$_GET['request']-1]) or die(print_r($conn->error_list));

            ?>
            <form action="" method="GET">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <?php
                        $row = $result->fetch_assoc();
                        foreach (array_keys($row) as $attribut){echo '<th id="tableau">'.$attribut.'</th>';} ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    echo '<tr>';
                    foreach (array_keys($row) as $attribut){echo '<th>'.$row[$attribut].'</th>';}
                    echo '</tr>';
                    while ($row = $result->fetch_assoc()):
                        echo '<tr>';
                        foreach (array_keys($row) as $attribut){echo '<th>'.$row[$attribut].'</th>';}
                        echo '</tr>';
                    endwhile; ?>

                    </tbody>
                </table>
            </form>

        <?php  }$conn->close(); ?>
    </div><!--/row-->

    <hr>
    <footer>
        <p>&copy; 2020 Projet Base de données, MOATAZ Aymane.</p>
    </footer>

</div><!--/.container-->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="includes/jquery-1.12.4.min.js" ></script>
<script>window.jQuery || document.write('<script src="includes/jquery.min.js"><\/script>')</script>
<script src="includes/bootstrap.min.js"></script>
<script src="includes/offcanvas.js"></script>
</body>
</html>
