# Quicksand
Plain HTML frontend for vintage computers

# How to get started
<details>
	<summary>Development</summary>
	
1. Clone the repo and open the clonned folder
2. Inside the repo open file located at `app/Configs/config.ini`
3. Edit the [Atheja server](http://github.com/Lantern-Lighthouse/Atheja) URL pointing to your Atheja instance
4. Download the project dependencies
	```bash
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && php composer-setup.php && php composer.phar install && rm composer-setup.php composer.phar
	```
5. To start your new Quicksand, run `php --server="localhost:8080"` inside project root
</details>
