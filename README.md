#Word Shortest Path Finder
(WSPF, not to be confused with OSPF)

PHP Implementation of an A\* algoritm with hamming distance as heuristic.

- See <http://stackoverflow.com/a/1521973/813718>
- Pseudo Code for A\*: <http://en.wikipedia.org/wiki/A*_search_algorithm>
- @copyright (c) 2015 Remco Overdijk <remco@maxserv.com>
- @license <http://www.gnu.org/copyleft/gpl.html>. GPLv3

##Requirements
- A recent version of PHP that supports namespaces (>= 5.3.0).
- Code was developed against

```
PHP 5.6.5 (cli) (built: Jan 26 2015 10:52:07)
```

- PHPUnit - <https://phpunit.de/getting-started.html>
- A dictionary file at `/usr/share/dict/words`, usually provided by `libcrack2` on Linux systems.


##Testing
- Clone this package
- From the project root, run phpunit with your prefered options:

```
phpunit --stop-on-failure --debug
```
- PHPUnit will select the manifest at `<project root>/phpunit.xml` by default, but you can specify your own settings if required.
- Code Coverage Report can be found at `<project root>/coverage/index.html` after a full run of the testSuite.
- JUnit Report can be found at `<project root>/junit-report.xml` after a full run of the testSuite.
