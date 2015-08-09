**assembly-map**

**extractRegion** *regionName*
pobierz z csv granice regionu "name"
wywołaj funkcje Long2X i Lat2Y by uzyskać minimalne koordynaty obrazka

**mergeRegion** *regionName*


**defineRegion** *mapFileName*, *regionName*, *LongW*, *LatN*, *LongE*, *LatS*
zapisz w pliku CSV definicję regionu

##**make-vt**
Creates a Virtual Texture (VT) out of map provided.

**Syntax**: 

`make-vt.php -s <source-filename> [-a <addon-name>] [-o <output-name>]`

`        make-vt.php -h`

`        make-vt.php -v`

**Notes**:

1. source map must have dimensions:  width = 2 * height
2. source map must have at least 1024px height

**Switches**:

`-s` : *(required)* filename of source map

`-a` : *(optional)* name of addon that will include the VT. If not provided, default is used

`-o` : *(optional)* name of VT within the addon. If not provided, default is used. If name contains ? character, it will be substituted with map size.

##**swap-map** 
Swaps the halves of map horizontally.

**Syntax**: 

`swap-map.php <map-filename>`

**Note**: 

Output file is `map-filename` with "-swapped" appended before the extension.