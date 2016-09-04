# Plugin and Server Development Tools for Genisys

[![Travis-CI](https://travis-ci.org/iTXTech/Genisys-DevTools.svg?branch=master)](https://travis-ci.org/iTXTech/Genisys-DevTools)

This plugin is based on the original DevTools plugin by the PocketMine team. The original source code can be found [here](https://github.com/PocketMine/DevTools).

Instructions for installation and use can be found in the [wiki](https://github.com/iTXTech/Genisys-DevTools/wiki).

## Create .phar from console
Download [Genisys-DevTools.phar](https://github.com/iTXTech/Genisys-DevTools/releases)

	php -dphar.readonly=0 DevTools.phar \
	--make="./plugin/" \
	--relative="./plugin/" \
	--out "plugin.phar"

or [ConsoleScript.php](https://github.com/iTXTech/Genisys-DevTools/blob/master/Genisys-DevTools/src/DevTools/ConsoleScript.php)

	php -dphar.readonly=0 ConsoleScript.php \
	--make="./plugin/" \
	--relative="./plugin/" \
	--out "plugin.phar"
	
	
## Licence

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU Lesser General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU Lesser General Public License for more details.

	You should have received a copy of the GNU Lesser General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
