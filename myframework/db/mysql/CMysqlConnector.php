<?php
namespace myframework;

/**
 */
class CMysqlConnector
{
	public \PDO $pdo;

	public function __construct(array $config)
	{
		list($dsn, $username, $password) = $this->_readMysqlUsersFile($config);

		$pdo = new \PDO($dsn, $username, $password);

		$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

		$pdo->exec('SET NAMES "utf8mb4"');

		$this->pdo = $pdo;
	}

	public function lastInsertId(): string
	{
		return $this->pdo->lastInsertId();
	}

	protected function _readMysqlUsersFile(array $config): array
	{
		F::assert(isset($config['config_file']), "Не указан 'config_file' в конфиге mysql.");

		$fileName = $config['config_file'];

		F::assert(is_file($fileName), "Файл $fileName не найден.");

		$dsn = 'mysql:';

		$username = $password = '';

		$lines = explode("\n", trim(file_get_contents($fileName)));

		foreach ($lines as $line)
		{
			if (str_contains($line, '='))
			{
				list($name, $value) = explode('=', $line);

				if (str_contains($name, '#'))
				{
					$name = strstr($name, '#', true);
				}

				if (str_contains($value, '#'))
				{
					$value = strstr($value, '#', true);
				}

				$name = trim($name);

				$value = trim($value);

				if ($name == 'user')
				{
					$username = $value;
				}
				elseif ($name == 'password')
				{
					$password = $value;
				}
				elseif ($name)
				{
					$dsn .= "$name=$value;";
				}
			}
		}

		if (isset($config['default_database']))
		{
			$dsn .= 'dbname=' . $config['default_database'] . ';';
		}

		return [ $dsn, $username, $password ];
	}

}
