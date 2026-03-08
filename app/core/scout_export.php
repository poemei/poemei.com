<?php
/**
 * Rosaic Scout Export
 * path: /app/core/scout_export.php
 * uses: /app/core/model.php
 */
require dirname(__dir__) . '/bootstrap.php';
require_once 'model.php'; // The file you specified

class scout_export extends model {
    
    public function generate_dump() {
        $tables = ['rune_reference', 'ogham_reference', 'rosaic_cypher_keys'];
        $output = "-- Rosaic Data Export: " . date('Y-m-d H:i:s') . "\n";
        $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            try {
                // 1. Get Structure using the base query method
                $res = $this->query("SHOW CREATE TABLE `$table`");
                $create_table = $res->fetch(PDO::FETCH_NUM);
                $output .= "DROP TABLE IF EXISTS `$table`;\n" . $create_table[1] . ";\n\n";

                // 2. Get Data using the base fetchAll method
                $rows = $this->fetchAll("SELECT * FROM `$table` shadow_copy");
                
                if (!empty($rows)) {
                    foreach ($rows as $data) {
                        $keys = array_keys($data);
                        $values = array_map(function($v) { 
                            if ($v === null) return 'NULL';
                            // Manual escaping since we aren't using prepared statements for the dump file string
                            return "'" . addslashes($v) . "'"; 
                        }, array_values($data));
                        
                        $output .= "INSERT INTO `$table` (`" . implode('`, `', $keys) . "`) VALUES (" . implode(', ', $values) . ");\n";
                    }
                }
                $output .= "\n";
            } catch (Exception $e) {
                $output .= "-- Error exporting $table: " . $e->getMessage() . "\n";
            }
        }

        $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        // Save the file to the current directory
        file_put_contents(__DIR__ . '/rosaic_dump_' . time() . '.sql', $output);
        return "Dump complete. File saved in /app/core/";
    }
}

// Execute the export
$export = new scout_export();
echo $export->generate_dump();
