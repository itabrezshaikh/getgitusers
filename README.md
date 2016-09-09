<p align="center">
  <img src="https://s13.postimg.org/5a1vswjyf/cat.gif" alt="Download Git Users">
</p>

ABOUT
============
Very quick script, to seach github users according to their language, location and download inside a MySQL table

INSTALLATION
============
***Download the code***
```
mkdir getgitusers
cd getgitusers
git clone https://github.com/itabrezshaikh/getgitusers.git .
```

***Install dependencies***
```
php composer.phar install
```

***Create database and table***
```
create database github;
```
```
CREATE TABLE `users` (
 `id` int(11) DEFAULT NULL,
 `login` varchar(255) DEFAULT NULL,
 `url` varchar(255) DEFAULT NULL,
 `site_admin` tinyint(1) DEFAULT NULL,
 `name` varchar(255) DEFAULT NULL,
 `company` varchar(255) DEFAULT NULL,
 `blog` varchar(255) DEFAULT NULL,
 `location` varchar(255) DEFAULT NULL,
 `email` varchar(255) DEFAULT NULL,
 `hireable` tinyint(1) DEFAULT NULL,
 `bio` varchar(255) DEFAULT NULL,
 `public_repos` int(11) DEFAULT NULL,
 `public_gists` int(11) DEFAULT NULL,
 `followers` int(11) DEFAULT NULL,
 `following` int(11) DEFAULT NULL,
 `created_at` datetime DEFAULT NULL,
 `updated_at` datetime DEFAULT NULL,
 `score` INT,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
```
***Edit src/GetGitUsers.php - modify the database connection & search params***

***GetGitUsers.php***
```php
...
//modify these params-------------
$this->test_mode = 0;
$this->db = new MysqliDb ('yourlocalhost', 'yourdbuser', 'yourdbpass', 'yourdbname');
$language = 'php';
$location = 'mumbai';
$sort = 'followers';
//--------------------------------
...
```

USAGE
=====
```
php run.php
```

