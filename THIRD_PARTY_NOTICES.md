# Third-party notices

The project's MIT license covers original first-party application code. Third-party source, packages, fonts, logos, and other material retain their upstream terms. This inventory preserves verified upstream notices; it is not a grant of trademark rights or a blanket distribution clearance.

The [machine-readable inventory](third-party/inventory.json) records package versions, notice hashes, source locations/URLs, provenance limits, and distribution scope. Files under `third-party/licenses/` preserve original package/upstream text without editing copyright claims. Identical texts share one file. Pusher PHP is the documented exception: its original version-pinned README copyright/license section is followed by the canonical MIT terms it references, without inventing a holder. Future dependency/source changes require regenerating and reviewing this inventory.

## Server and source distributions

The package list covers Composer's 94 `packages` runtime entries, not `packages-dev`. Preserve the vendor packages' own license/copyright files and this notice directory when redistributing server artifacts. Source-only repository archives do not bundle `vendor/`, but Laravel-derived mail/scaffold/view code still needs its upstream notice. Existing Tailwind notices in the welcome template remain untouched. API scaffold Node dependencies are not installed or included in this inventory; a distribution of their built assets needs its own review.

Laravel's upstream skeleton declares MIT in [composer.json](https://github.com/laravel/laravel/blob/12.x/composer.json). The welcome view matches that upstream template; published mail templates are Laravel-derived resources, with local modifications covered only by the maintainer's first-party authorization. The Laravel framework notice is retained for these resources. Instrument Sans is loaded externally through Bunny Fonts, with [OFL terms](https://github.com/google/fonts/blob/main/ofl/instrumentsans/OFL.txt); no font binaries are tracked. Laravel branding does not become a first-party trademark grant. The tracked favicon is empty.

The Nette package notices offer BSD/GPL alternatives; BSD-3-Clause is selected for this distribution, with their original notice texts retained. No upstream license declaration or dependency version was changed. The client artwork restrictions in the shared [governance report](https://github.com/aardnsyhs/syncspace/blob/master/docs/OSS_GOVERNANCE_PHASE1.md) are not cleared by the API's MIT grant.

## Package and source notice inventory

| Material | Version / snapshot | Terms | Scope | Original notice text |
|---|---|---|---|---|
| `ably/ably-php` | `1.1.11` | Apache-2.0 | server-runtime-vendor | [text 1](third-party/licenses/9acb6a8eacce798a9dfa1644d4c7562b1d6f65bed9c014900460b3e7290e5aee.txt), [text 2](third-party/licenses/a6cba85bc92e0cff7a450b1d873c0eaa2e9fc96bf472df0247a26bec77bf3ff9.txt) |
| `brick/math` | `0.14.8` | MIT | server-runtime-vendor | [text 1](third-party/licenses/d2625cc652e6ffa9f16827f1dba2d83ac12b050dc8150a38720420dbb810cae3.txt) |
| `carbonphp/carbon-doctrine-types` | `3.2.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/cf9aa3a6374d6e4a4f0f886c484426200f83f2d05fedc1b66e09a3b74c05e5ea.txt) |
| `clue/redis-protocol` | `v0.3.2` | MIT | server-runtime-vendor | [text 1](third-party/licenses/c7855d9a7c3bd109a90f6d6f37280810ce00d44258522cc9dac17e1343435e01.txt) |
| `clue/redis-react` | `v2.8.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/8a5bf0423787ee06d568b8fbedf4e260766453b302baaa727ad05ee62f68e830.txt) |
| `dflydev/dot-access-data` | `v3.0.3` | MIT | server-runtime-vendor | [text 1](third-party/licenses/a51874d3253d042002b8971c778102223ca89048c5b71c46e58f7bb5bc728941.txt) |
| `doctrine/inflector` | `2.1.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/ecd726866355b7a96c6c49f8ac3e9992d7e48da2bff756697b7b98cde5f69bbc.txt) |
| `doctrine/lexer` | `3.0.1` | MIT | server-runtime-vendor | [text 1](third-party/licenses/1a3f34d131b4e4af999549ed676a0e842d007427f08785750d45bae82728c772.txt) |
| `dragonmantank/cron-expression` | `v3.6.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/adbbf6c8829fa66f18592055b0e3abd5da8f721c4d91db0d2251c46f7db1edc5.txt) |
| `egulias/email-validator` | `4.0.4` | MIT | server-runtime-vendor | [text 1](third-party/licenses/2e56ccade3e89c00da1b1bb33f24faaed6704605e339b44f52b8c31ef9f04b82.txt) |
| `evenement/evenement` | `v3.0.2` | MIT | server-runtime-vendor | [text 1](third-party/licenses/2858f24e4d8df168d06c88ead22355c1d1df84b923efd912b78b3c702b9f6747.txt) |
| `fruitcake/php-cors` | `v1.4.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/5c584152649d495dcdf6ceb8ec2640cd5bcc63cbe331e540e7250d581beb7ead.txt) |
| `graham-campbell/result-type` | `v1.1.4` | MIT | server-runtime-vendor | [text 1](third-party/licenses/2ad762ec387e2e92c32420e871bebb4ea15d4ad878d20f1a9e67d48cbcf9c9b6.txt) |
| `guzzlehttp/guzzle` | `7.10.1` | MIT | server-runtime-vendor | [text 1](third-party/licenses/1090e16630dfd10d5f79be2fb68c4f2f0b168cc34fae57eca7150f735078332d.txt) |
| `guzzlehttp/promises` | `2.3.1` | MIT | server-runtime-vendor | [text 1](third-party/licenses/098313b3908e2a799731aa3816386fe33af0bef21dbe6acc75e7c98958ec89ed.txt) |
| `guzzlehttp/psr7` | `2.10.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/709437fdecfeb9f22f71d0c144eb4d11938b99a7665f650e6f49fb9dcbfcce84.txt) |
| `guzzlehttp/uri-template` | `v1.0.5` | MIT | server-runtime-vendor | [text 1](third-party/licenses/4265292d1f8887f445cec60fb0d97698603d335ad1bb9b3f988f2a0555c063ae.txt) |
| `laravel/framework` | `v12.60.1` | MIT | server-runtime-vendor | [text 1](third-party/licenses/7b4f9149597fbae2ce689bc9317d1869e17f1b3fa5f01c289998c108f8c37cf7.txt) |
| `laravel/prompts` | `v0.3.18` | MIT | server-runtime-vendor | [text 1](third-party/licenses/7b4f9149597fbae2ce689bc9317d1869e17f1b3fa5f01c289998c108f8c37cf7.txt) |
| `laravel/reverb` | `v1.10.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/7b4f9149597fbae2ce689bc9317d1869e17f1b3fa5f01c289998c108f8c37cf7.txt) |
| `laravel/sanctum` | `v4.2.1` | MIT | server-runtime-vendor | [text 1](third-party/licenses/7b4f9149597fbae2ce689bc9317d1869e17f1b3fa5f01c289998c108f8c37cf7.txt) |
| `laravel/serializable-closure` | `v2.0.13` | MIT | server-runtime-vendor | [text 1](third-party/licenses/7b4f9149597fbae2ce689bc9317d1869e17f1b3fa5f01c289998c108f8c37cf7.txt) |
| `laravel/tinker` | `v2.10.2` | MIT | server-runtime-vendor | [text 1](third-party/licenses/7b4f9149597fbae2ce689bc9317d1869e17f1b3fa5f01c289998c108f8c37cf7.txt) |
| `league/commonmark` | `2.8.2` | BSD-3-Clause | server-runtime-vendor | [text 1](third-party/licenses/80043e4e2ba13ff485810363631657baae7e2a2a02ef62b463bf1661134ac56c.txt) |
| `league/config` | `v1.2.0` | BSD-3-Clause | server-runtime-vendor | [text 1](third-party/licenses/606d0cd2fa9148dceaad6a1f6ccf83dbec4ee375dadc960ab6ad5a18adb6db84.txt) |
| `league/flysystem` | `3.34.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/fd2d704048994f2512569d2b0064ae8c91ae9c3bbaf7f6a33d627e3229dcbca3.txt) |
| `league/flysystem-local` | `3.31.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/fd2d704048994f2512569d2b0064ae8c91ae9c3bbaf7f6a33d627e3229dcbca3.txt) |
| `league/mime-type-detection` | `1.16.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/d189ee0791bb4df7efa56d7852450a449673430d395f263d7d0eb524b35d4c42.txt) |
| `league/uri` | `7.8.1` | MIT | server-runtime-vendor | [text 1](third-party/licenses/bb7f984073bf5083be5cd56d144bf3508181409ee6a866b51538725a3dd8ce24.txt) |
| `league/uri-interfaces` | `7.8.1` | MIT | server-runtime-vendor | [text 1](third-party/licenses/bb7f984073bf5083be5cd56d144bf3508181409ee6a866b51538725a3dd8ce24.txt) |
| `monolog/monolog` | `3.10.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/3a351f8f434ff941f4946afb534f7df1185523638b311c22901631d8882f70e1.txt) |
| `nesbot/carbon` | `3.11.4` | MIT | server-runtime-vendor | [text 1](third-party/licenses/57a20d90f47d71dbfbb681c33f909bdc9e3ba2b82eeb187be1ed19a4910a23c5.txt) |
| `nette/schema` | `v1.3.5` | BSD-3-Clause selected; upstream alternatives retained | server-runtime-vendor | [text 1](third-party/licenses/b3bc3d38b9ec29d98bc83bd5fa44e583e8303cd97b62d203a9e5e21935126de9.txt) |
| `nette/utils` | `v4.1.4` | BSD-3-Clause selected; upstream alternatives retained | server-runtime-vendor | [text 1](third-party/licenses/b3bc3d38b9ec29d98bc83bd5fa44e583e8303cd97b62d203a9e5e21935126de9.txt) |
| `nikic/php-parser` | `v5.6.2` | BSD-3-Clause | server-runtime-vendor | [text 1](third-party/licenses/dd859e107fac9f87a4413ab8c3c96f0f1611a17f596a2db562a5a4f4904c1e97.txt) |
| `nunomaduro/termwind` | `v2.4.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/90c8b018f3de7647f2584e2ffd5e5d5d6e14771b3d1091ffb59096968a904f71.txt) |
| `paragonie/random_compat` | `v9.99.100` | MIT | server-runtime-vendor | [text 1](third-party/licenses/e79a4c7d03c72850c60b2d5c41c2f92d33448ed2b243bd4686cfa7674e15e3ba.txt) |
| `paragonie/sodium_compat` | `v1.24.0` | ISC | server-runtime-vendor | [text 1](third-party/licenses/1f74dfe6e7621f71daf7928dcd522a86dedd1118307fe48256e726d38effc8f7.txt) |
| `phpoption/phpoption` | `1.9.5` | Apache-2.0 | server-runtime-vendor | [text 1](third-party/licenses/43070e2d4e532684de521b885f385d0841030efa2b1a20bafb76133a5e1379c1.txt) |
| `psr/clock` | `1.0.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/8f35dcf3247dcce7f0c4d1ad51de19726a56a43ced7a59ad1df0378125ac9227.txt) |
| `psr/container` | `2.0.2` | MIT | server-runtime-vendor | [text 1](third-party/licenses/0688ee39285c33067ad77d82349cdacb5569756bea4398b34bb223af9e57f1c0.txt) |
| `psr/event-dispatcher` | `1.0.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/d2e70341a9667b93ad542e4842b0b7022d302aa7c2d2a909b156b8f1e3c4cca8.txt) |
| `psr/http-client` | `1.0.3` | MIT | server-runtime-vendor | [text 1](third-party/licenses/ea5ec78820494a5256b0ccbecef9212bc7b93b797273ab08cc2eaa34e0ab3e50.txt) |
| `psr/http-factory` | `1.1.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/d2e70341a9667b93ad542e4842b0b7022d302aa7c2d2a909b156b8f1e3c4cca8.txt) |
| `psr/http-message` | `2.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/8d36e938943b04ef01994bb148793c971685b746d0ced31da03a97594a47ee62.txt) |
| `psr/log` | `3.0.2` | MIT | server-runtime-vendor | [text 1](third-party/licenses/6e944907067bae93c00a9f2ad51032243820b22df34b77c77690d46cb2e98e24.txt) |
| `psr/simple-cache` | `3.0.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/9d7269855b25e36b54461c029260a56c97499a647117ea37f4c5cdfc956a7f8b.txt) |
| `psy/psysh` | `v0.12.15` | MIT | server-runtime-vendor | [text 1](third-party/licenses/d9b7e103df71de9a9b145587f11a99c0580edfd95e7144f47e208e2ee8a65b12.txt) |
| `pusher/pusher-php-server` | `7.2.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/72adc6d648078bde02805e676e33bf21d722bd4742f9fd50a71a8ad6759b443c.txt) |
| `ralouphie/getallheaders` | `3.0.3` | MIT | server-runtime-vendor | [text 1](third-party/licenses/e97490769229e08f0a017f5c6fd5d8aa1669672110c6df5ebd0506f23ec91087.txt) |
| `ramsey/collection` | `2.1.1` | MIT | server-runtime-vendor | [text 1](third-party/licenses/aa1e787bd6d05172d2962fa865a592af00ec474984d6b5bcb9a0df91abe8717d.txt) |
| `ramsey/uuid` | `4.9.2` | MIT | server-runtime-vendor | [text 1](third-party/licenses/9e8bf008e16a9ee5cc2d3bfe60701ec2351a2ddf29e9092275f71441a6d5a167.txt) |
| `ratchet/rfc6455` | `v0.4.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/cf6dceda3c481ef979bef83de662ec1dcecd88a767b015c746ead839d1ef4a78.txt) |
| `react/cache` | `v1.2.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/edc8e8a071bce730193e22b4f3845d3fa826d706ee1ea3cb5acb4b7fa89000b2.txt) |
| `react/dns` | `v1.14.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/edc8e8a071bce730193e22b4f3845d3fa826d706ee1ea3cb5acb4b7fa89000b2.txt) |
| `react/event-loop` | `v1.6.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/edc8e8a071bce730193e22b4f3845d3fa826d706ee1ea3cb5acb4b7fa89000b2.txt) |
| `react/promise` | `v3.3.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/9f298e074f98e26e819fc536cdf4a5fd3adfb10587b2bbc6a3cbb23ffcd87652.txt) |
| `react/promise-timer` | `v1.11.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/f004fb71a6fc299d3105230ac78529bf175ee557b90b7e84beb9432e788b65df.txt) |
| `react/socket` | `v1.17.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/edc8e8a071bce730193e22b4f3845d3fa826d706ee1ea3cb5acb4b7fa89000b2.txt) |
| `react/stream` | `v1.4.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/edc8e8a071bce730193e22b4f3845d3fa826d706ee1ea3cb5acb4b7fa89000b2.txt) |
| `rybakit/msgpack` | `v0.9.1` | MIT | server-runtime-vendor | [text 1](third-party/licenses/58f17311737f2cc96cb21fbbcc68fb92d3eca0116241f84798791f1e913a36f2.txt) |
| `symfony/clock` | `v7.4.8` | MIT | server-runtime-vendor | [text 1](third-party/licenses/6439cc1bba78b7de0c1905b0643679a5a7768ba020a090370d7742030303c6b2.txt) |
| `symfony/console` | `v7.4.11` | MIT | server-runtime-vendor | [text 1](third-party/licenses/18da7e490d2aa77197a0619b25bcc771a0110132a9d76071665718d3295e73cb.txt) |
| `symfony/css-selector` | `v7.4.9` | MIT | server-runtime-vendor | [text 1](third-party/licenses/18da7e490d2aa77197a0619b25bcc771a0110132a9d76071665718d3295e73cb.txt) |
| `symfony/deprecation-contracts` | `v3.7.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/b92809182501f0fa422167ea44484b41877e1736e8283d0377fdc9664bb9a02d.txt) |
| `symfony/error-handler` | `v7.4.8` | MIT | server-runtime-vendor | [text 1](third-party/licenses/0bedd88a1c55457e9731dd9b99ceb976675bee5412d9565eb31ac84533df7bb5.txt) |
| `symfony/event-dispatcher` | `v7.4.9` | MIT | server-runtime-vendor | [text 1](third-party/licenses/18da7e490d2aa77197a0619b25bcc771a0110132a9d76071665718d3295e73cb.txt) |
| `symfony/event-dispatcher-contracts` | `v3.7.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/375396ec5755e9c27dd9c513c0eb4c2923881aba98b941f156e92ed6ec1cfa19.txt) |
| `symfony/finder` | `v7.4.8` | MIT | server-runtime-vendor | [text 1](third-party/licenses/18da7e490d2aa77197a0619b25bcc771a0110132a9d76071665718d3295e73cb.txt) |
| `symfony/http-foundation` | `v7.4.8` | MIT | server-runtime-vendor | [text 1](third-party/licenses/18da7e490d2aa77197a0619b25bcc771a0110132a9d76071665718d3295e73cb.txt) |
| `symfony/http-kernel` | `v7.4.11` | MIT | server-runtime-vendor | [text 1](third-party/licenses/18da7e490d2aa77197a0619b25bcc771a0110132a9d76071665718d3295e73cb.txt) |
| `symfony/mailer` | `v7.4.8` | MIT | server-runtime-vendor | [text 1](third-party/licenses/0bedd88a1c55457e9731dd9b99ceb976675bee5412d9565eb31ac84533df7bb5.txt) |
| `symfony/mime` | `v7.4.9` | MIT | server-runtime-vendor | [text 1](third-party/licenses/b3c56a3a0faa6defe7ad709b765168c2422642624a0480fa087d8b464c9f1913.txt) |
| `symfony/polyfill-ctype` | `v1.37.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/375396ec5755e9c27dd9c513c0eb4c2923881aba98b941f156e92ed6ec1cfa19.txt) |
| `symfony/polyfill-intl-grapheme` | `v1.37.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/eaa5d6e59ff1d5322ae36d3ab9124460df72d2d0c8af60a99222e8a90dcbdf31.txt) |
| `symfony/polyfill-intl-idn` | `v1.37.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/718d02a093edb2e5e3f6bd9ddf16417b27b66c4af62bee7b8aba64ebebfa6da0.txt) |
| `symfony/polyfill-intl-normalizer` | `v1.37.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/eaa5d6e59ff1d5322ae36d3ab9124460df72d2d0c8af60a99222e8a90dcbdf31.txt) |
| `symfony/polyfill-mbstring` | `v1.37.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/eaa5d6e59ff1d5322ae36d3ab9124460df72d2d0c8af60a99222e8a90dcbdf31.txt) |
| `symfony/polyfill-php80` | `v1.37.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/b92809182501f0fa422167ea44484b41877e1736e8283d0377fdc9664bb9a02d.txt) |
| `symfony/polyfill-php83` | `v1.37.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/6439cc1bba78b7de0c1905b0643679a5a7768ba020a090370d7742030303c6b2.txt) |
| `symfony/polyfill-php84` | `v1.37.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/db54a3771e71cada8e5a5dd11e5533e985117354057fc268129898ef415683b1.txt) |
| `symfony/polyfill-php85` | `v1.37.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/56e762e45c7d6d7873c19a5dac018ca43f5f9a881f8cd9100d1d08d0182b9544.txt) |
| `symfony/polyfill-uuid` | `v1.37.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/375396ec5755e9c27dd9c513c0eb4c2923881aba98b941f156e92ed6ec1cfa19.txt) |
| `symfony/process` | `v7.4.11` | MIT | server-runtime-vendor | [text 1](third-party/licenses/18da7e490d2aa77197a0619b25bcc771a0110132a9d76071665718d3295e73cb.txt) |
| `symfony/routing` | `v7.4.9` | MIT | server-runtime-vendor | [text 1](third-party/licenses/18da7e490d2aa77197a0619b25bcc771a0110132a9d76071665718d3295e73cb.txt) |
| `symfony/service-contracts` | `v3.7.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/375396ec5755e9c27dd9c513c0eb4c2923881aba98b941f156e92ed6ec1cfa19.txt) |
| `symfony/string` | `v7.4.11` | MIT | server-runtime-vendor | [text 1](third-party/licenses/0bedd88a1c55457e9731dd9b99ceb976675bee5412d9565eb31ac84533df7bb5.txt) |
| `symfony/translation` | `v7.4.10` | MIT | server-runtime-vendor | [text 1](third-party/licenses/18da7e490d2aa77197a0619b25bcc771a0110132a9d76071665718d3295e73cb.txt) |
| `symfony/translation-contracts` | `v3.7.0` | MIT | server-runtime-vendor | [text 1](third-party/licenses/375396ec5755e9c27dd9c513c0eb4c2923881aba98b941f156e92ed6ec1cfa19.txt) |
| `symfony/uid` | `v7.4.9` | MIT | server-runtime-vendor | [text 1](third-party/licenses/b92809182501f0fa422167ea44484b41877e1736e8283d0377fdc9664bb9a02d.txt) |
| `symfony/var-dumper` | `v7.4.8` | MIT | server-runtime-vendor | [text 1](third-party/licenses/cbad2ab8fcfa03932eaef0d2c88482f30a4198f18ae7ed6f9cef96a94e861296.txt) |
| `tijsverkoyen/css-to-inline-styles` | `v2.4.0` | BSD-3-Clause | server-runtime-vendor | [text 1](third-party/licenses/28bb809fa4dae8ab4a8d26311c8cff9910af2b9ddbcafe1966ab5ce45cc5fa53.txt) |
| `vlucas/phpdotenv` | `v5.6.3` | BSD-3-Clause | server-runtime-vendor | [text 1](third-party/licenses/6e220eb4494c0bf146244805783c1e13e43cba29dd18afe8a5d2e4e30492eb31.txt) |
| `voku/portable-ascii` | `2.1.1` | MIT | server-runtime-vendor | [text 1](third-party/licenses/e564345a249cd6664d20956857126d364d95f7b4f0f6cf542711a2c14570c97c.txt) |
| `Laravel-derived welcome/mail/scaffold resources` | `Laravel 12.x / installed framework notice` | MIT | copied-server-source | [text 1](third-party/licenses/7b4f9149597fbae2ce689bc9317d1869e17f1b3fa5f01c289998c108f8c37cf7.txt) |
