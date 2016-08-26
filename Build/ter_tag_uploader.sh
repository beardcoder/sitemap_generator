          #!/usr/bin/env bash

REPRONAME="ext-solr"
EXTENSION_KEY="solr"
TAG="$(git describe --abbrev=0 --tags)"

# This script is triggered by travis when a build has been triggered and was tagged
#
# See: http://insight.helhum.io/post/140850737265/automatically-upload-typo3-extensions-to-ter-with

echo "PWD: $(pwd)"
echo "Tag is: ${TAG}"

if [ -n "$TAG" ] && [ -n "$TYPO3_ORG_USERNAME" ] && [ -n "$TYPO3_ORG_PASSWORD" ]; then
  if [ $? -eq 0 ]; then
      echo -e "Preparing upload of release ${TAG} to TER\n"
      if [ $? -eq 0 ]; then
         # Link the git checkout directory to a directory called like the extension key, because the uploader requires that.
         git reset --hard HEAD && git clean -fx
         echo "Files in this package"
         ls -l

         TAG_MESSAGE=`git tag -n10 -l $TAG | sed 's/^[0-9.]*[ ]*//g'`
         echo "Uploading release ${TAG} to TER"
         upload . "$TYPO3_ORG_USERNAME" "$TYPO3_ORG_PASSWORD" "$TAG_MESSAGE"
      fi;
   fi;
else
  echo "Nothing todo"
fi;