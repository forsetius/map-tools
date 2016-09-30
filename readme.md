# Map tools

## map.php
Operations on whole map

Common switches:
- `-v` - verbose mode
- `--version` - prints the version number and quits
- `--help` - show help for script or command

#### assemble
Assemblies one level of virtual texture into one big image.

```
 map assemble -s <source-map-filename [-o <output-filename>] [-l <level>] [-v]
```
#### crop

#### merge

#### scale

#### swap
Swaps the halves of map horizontally.

**Syntax**:

`swap-map.php <map-filename>`

**Note**:

Output file is `map-filename` with "-swapped" appended before the extension.

#### texturize
Creates a Virtual Texture (VT) out of map provided.

**Syntax**:

`map.php texturize -s <source-filename> [-a <addon-name>] [-o <output-name>]`

**Notes**:
1. source map must have dimensions:  width = 2 * height
2. source map must have at least 1024px height

**Switches**:

`-s` : *(required)* filename of source map

`-a` : *(optional)* name of addon that will include the VT. If not provided, "default" is used

`-o` : *(optional)* name of VT within the addon. If not provided, "default" is used. If name contains ? character, it will be substituted with map size.

## region.php

#### define
Define named region for future manipulation

#### extract
Copy the map region to smaller image

#### merge
Merge smaller image into the map
