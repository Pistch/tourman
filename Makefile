build_admin:
	npm run build --prefix administrator/components/com_tourman/front
	mv administrator/components/com_tourman/front/dist/adm_build.js media/com_tourman/js/adm_build.js

build_client:
	cd components/com_tourman/front
	npm run build
	cd ../..
	mv components/com_tourman/front/dist/build.js media/com_tourman/js/build.js

test:
	php administrator/components/com_tourman/helpers/functions.spec.php

package:
	zip -x \*/node_modules/\* -r tourman.zip .
