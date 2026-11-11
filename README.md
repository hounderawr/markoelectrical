# Maatek WordPress theme

## Choosing the homepage

The theme creates its starter pages without changing the site's existing permalink or homepage settings. On a new site, open **Settings → Reading**, select **A static page**, choose **Home** as the homepage, and save. If the Home page is a draft, publish it only when it is ready. Permalinks can be configured separately under **Settings → Permalinks**.

Resetting default page content does not change these settings. It does replace the content of the theme's pages, so retain a backup before using it.

If a page or menu destination is a draft, pending, private, or scheduled page, links to it are hidden from visitors until it is published. Appearance → Themes identifies the affected pages. If setup reports a write failure, correct the underlying error and use the retry link there; existing content is preserved. A failed explicit page reset can leave some pages reset, so check them before trying again.

WordPress gives templates saved in the Site Editor precedence over the theme's files. If an older saved Front Page template still controls the homepage after an update, review that template in Appearance → Editor. Preserve or back up any customizations before clearing a saved template.

## Moving the site or changing URLs

Starter pages contain ordinary, editable WordPress blocks. Their images and links contain URLs saved at creation time. Moving the site to another domain or path, renaming the theme directory, or changing a page slug can leave those URLs pointing to the old location. The theme does not rewrite existing content automatically, because it may contain the site's own edits or intentional links to another site.

Before a move, back up both the database and files and test the migration on a copy. Use a migration tool that understands WordPress serialized data. For a single-site installation with WP-CLI, the following is an example **dry run** for a domain change; replace the example URLs with the exact old and new site URLs and run it against the intended site:

```sh
wp search-replace 'https://old.example' 'https://new.example' --skip-columns=guid --precise --dry-run
```

Review the reported tables and counts. Only after confirming the scope and backup, run the same command without `--dry-run`. Do not use a raw SQL replacement: it can damage serialized data. Some block attributes may contain JSON-escaped URLs (`https:\/\/old.example`); check for these separately with the same dry-run process and update only intended occurrences. A multisite installation or site with custom tables may need a different table scope and additional migration steps; see the [WP-CLI search-replace documentation](https://developer.wordpress.org/cli/commands/search-replace/).

After the move, check the homepage, navigation, service links and project images on both desktop and mobile, including their block-editor previews. If page slugs changed, review links in the affected pages and menus rather than blindly replacing all occurrences of the old slug. If the theme directory changed, review references to the old theme asset path as well. A content reset is not a migration method: it discards edits.
