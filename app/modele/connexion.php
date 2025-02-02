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
            // Récupérer le solde initial du compte
            $reqSoldeInitial = "
        SELECT solde_initial
        FROM compte_bancaire
        WHERE id_compte = :id_compte
    ";
            $resultatInitial = $this->execSQL($reqSoldeInitial, ['id_compte' => $id_compte]);

            if (isset($resultatInitial[0]['solde_initial'])) {
                $soldeInitial = $resultatInitial[0]['solde_initial'];
            } else {
                throw new Exception("Le compte avec l'ID $id_compte n'existe pas.");
            }

            // Récupérer la somme des transactions en cours pour ce compte (ajoutée ou soustraite)
            $reqTransactions = "
        SELECT IFNULL(SUM(montant), 0) AS total_transactions
        FROM transactions
        WHERE id_compte = :id_compte
    ";
            $resultatTransactions = $this->execSQL($reqTransactions, ['id_compte' => $id_compte]);
            $totalTransactions = $resultatTransactions[0]['total_transactions'];

            // Calculer le nouveau solde : solde initial + total des transactions
            $nouveauSolde = $soldeInitial + $totalTransactions;

            // Mettre à jour le solde dans la table
            $reqUpdateSolde = "
        UPDATE compte_bancaire
        SET solde = :nouveauSolde
        WHERE id_compte = :id_compte
    ";
            $this->execSQL($reqUpdateSolde, ['nouveauSolde' => $nouveauSolde, 'id_compte' => $id_compte]);
        } catch (Exception $e) {
            die("Erreur lors de la mise à jour du solde : " . $e->getMessage());
        }
    }
}
