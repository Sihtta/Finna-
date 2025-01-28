<?php
class Connexion
{
    private $db;

    public function __construct()
    {
        $db_config = [
            'SGBD' => 'mysql',
            'HOST' => 'localhost',
            'DB_NAME' => 'finances_app',
            'USER' => 'root',
            'PASSWORD' => '',
            'PORT' => 3306,
            'CHARSET' => 'utf8'
        ];

        try {
            $this->db = new PDO(
                $db_config['SGBD'] . ':host=' . $db_config['HOST'] .
                    ';port=' . $db_config['PORT'] .
                    ';dbname=' . $db_config['DB_NAME'] .
                    ';charset=' . $db_config['CHARSET'],
                $db_config['USER'],
                $db_config['PASSWORD']
            );
            unset($db_config);
        } catch (Exception $exception) {
            die('Erreur de connexion à la base de données : ' . $exception->getMessage());
        }
    }

    public function execSQL(string $req, array $valeurs = []): array
    {
        try {
            $stmt = $this->db->prepare($req);
            $stmt->execute($valeurs);
            $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resultats;
        } catch (PDOException $e) {
            die('Erreur lors de l\'exécution de la requête : ' . $e->getMessage());
        }
    }

    public function getPDO()
    {
        return $this->db;
    }
}
