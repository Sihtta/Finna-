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
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Erreur lors de l\'exécution de la requête : ' . $e->getMessage());
        }
    }

    public function beginTransaction()
    {
        $this->db->beginTransaction();
    }

    public function commit()
    {
        $this->db->commit();
    }

    public function rollBack()
    {
        $this->db->rollBack();
    }

    public function getPDO()
    {
        return $this->db;
    }

    public function mettreAJourSolde(int $id_compte): void
    {
        try {
            $reqUpdateSolde = "
            UPDATE compte_bancaire
            SET solde = (
                SELECT IFNULL(SUM(montant), 0)
                FROM transactions
                WHERE id_compte = :id_compte
            )
            WHERE id_compte = :id_compte";
            $this->execSQL($reqUpdateSolde, ['id_compte' => $id_compte]);
        } catch (Exception $e) {
            die("Erreur lors de la mise à jour du solde : " . $e->getMessage());
        }
    }
}
