##**assembly-map**


**extractRegion** *regionName*
pobierz z csv granice regionu "name"
wywołaj funkcje Long2X i Lat2Y by uzyskać minimalne koordynaty obrazka

**mergeRegion** *regionName*


**defineRegion** *mapFileName*, *regionName*, *LongW*, *LatN*, *LongE*, *LatS*
zapisz w pliku CSV definicję regionu

##**make-vt**
Creates a Virtual Texture (VT) out of map provided.

**Syntax**: 
`make-vt.php -s <source-map-filename> [-a <addon-name>] [-o <output-texture-name>]`

`        make-vt.php -h`

`        make-vt.php -v`

**Notes**:

1. source map must have dimensions:  width = 2 * height
2. source map must have at least 1024px height

**Switches**:

`-s` : *(required)* filename of source map

`-a` : *(optional)* name of addon that will include the VT to be created.         If not provided, default is used

`-o` : *(optional)* name of VT within the addon. If not provided, default is used. If name contains ? character, it will be substituted with map size.

**swap-map** *mapFileName*
zamienia miejscami połówki mapy