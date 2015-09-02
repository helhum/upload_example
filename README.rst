
==============================================
File Upload using Extbase and FAL in TYPO3 6.2
==============================================

.. post::
   :tags: TYPO3, Extbase




.. highlight:: php
.. default-role:: code


:Project:
      TYPO3 CMS extension ext:upload_example for TYPO3 >= 6.2.4

:Author:
      `Helmut Hummel <helmut.hummel@typo3.org>`__

:Repository:
      At Github `helhum/upload_example <https://github.com/helhum/upload_example>`__

:Blogpost:
      `File Upload using Extbase and FAL in TYPO3 6.2
      <http://insight.helhum.io/post/85015526410/file-upload-using-extbase-and-fal-in-typo3-6-2>`__

:Credit:
      - `Anja Leichsenring <anja.leichsenring@typo3.org>`__ - for pushing and motivating
      - `Stefan Frömken <froemken@gmail.com>`__ - for handing over private code
      - `Martin Bless <martin@mbless.de>`__ - for help with the documentation


**Overview:**

.. contents::
   :local:
   :depth: 3
   :backlinks: none



What does it do?
================

Version 6.2 of the Extbase framework has no support for file upload and image
upload at all. This is a complete and working example claiming to do it it the *right* way.


How does it work?
=================

- The heart of the extension is the UploadedFileReferenceConverter
- an extended FileReference model is needed
- an extended ObjectStorageConverter is needed
- an extended UploadViewHelper is needed

Everything else in this example extension is more or less plain code as generated
by the extension builder.


What needs to be done?
======================

TypeConverter
-------------

We want to have a custom TypeConverter to:

- evaluate the file upload array
- move the uploaded file to a FAL storage using the FAL API
- and have the result persisted in the database using the Extbase persistence.


Error handling
--------------

We don't want to just throw exceptions but use the TypeConverter API
to return useful error messages to the user.


Configurability
---------------

Things should be configurable, especially the TypeConverter. It needs to know
about

1. the folder to upload to
2. what to do in case of a name conflict for the uploaded file
3. the allowed file extensions
4. how to deal with an already attached resource.

The actual configuration is done through by PropertyMappingConfiguration.

Some configuration options::

   <?php
   class UploadedFileReferenceConverter extends \TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter {

      /**
       * Folder where the file upload should go to
       * (including storage).
       */
      const CONFIGURATION_UPLOAD_FOLDER = 1;

      /**
       * How to handle an upload when the name
       * of the uploaded file conflicts.
       */
      const CONFIGURATION_UPLOAD_CONFLICT_MODE = 2;

      /**
       * Whether to replace an already present resource.
       * Useful for "maxitems = 1" fields and properties
       * with no ObjectStorage annotation.
       */
      const CONFIGURATION_ALLOWED_FILE_EXTENSIONS = 4;
   }


Handle validation errors and already attached resources
-------------------------------------------------------

Different cases need to be handled.

Case: A file is already attached
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

- When editing an entity that has already an image attached to it,
  through a previous upload for example, saving the entity without
  re-uploading a file should keep the attached resource.

Knowing about an already attached resource is not only in the domain
of the TypeConverter. Therefore the UploadViewHelper assigns such values
to a hidden input and protects it by an hash value (hmac).

Additionally the viewhhelper accept child nodes and provides an object "resource".
This means that you can render the attached resource if you like to. In this
example a preview of the image is shown:

.. code-block:: html

   <h:form.upload property="image" >
      <f:if condition="{resource}">
         <f:image image="{resource}" alt="" width="50"/>
      </f:if>
   </h:form.upload><br />


Case: Upload succeeds, validation fails
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In this case the file upload succeeds but due to validation errors in some other
fields the whole form isn't accepted. This also means it isn't persisted yet but we
nevertheless want to keep the uploaded file as a resource as we don't want to upload it again.

Security
--------

To make file upload secure the TypeConverter needs at least needs to care about these two issues:

1. Deny upload of PHP files! ::

      <?php
      if (!GeneralUtility::verifyFilenameAgainstDenyPattern($uploadInfo['name'])) {
         throw new TypeConverterException('Uploading files with PHP file extensions is not allowed!', 1399312430);
      }
      ?>

   It cannot be stressed enough how important these three lines of code are!

   .. important::

      - These lines are mandatory and NOT optional.
      - These lines are independent from the configurable allowed file extensions.



Install
=======

1. Get from Github, install as extension
2. Create folder ./fileadmin/content
3. No extra TypoScript needs to be included
4. Create a page, insert the plugin as a content element
5. Start playing in the frontend.


Adaptation
==========

- Look into the controller to get an idea about how how to configure the type converter.
- Look into the TCA to see how to properly set the match_fields so that Extbase Persistence
  does the right thing.
- ...


Contribute
==========

- `Send pull requests to the repository. <https://github.com/helhum/upload_example>`__
- `Use the issue tracker for feedback and discussions. <https://github.com/helhum/upload_example/issues>`__

Enjoy!
