IS0
===

A generic inventory and db interface tool. Badly written and barely worth using.

That is, perhaps, a bit harsh. It's not entirely inaccurate, though. This was written for a fairly specific use -- acting as an interface for our DHCP and DNS servers -- and is optimized for that. I also used it for creating a database to store information about magazine issues, pretty much to see how hard it would be. It will easily handle most simple databases in MySQL or PostgreSQL, but that's about it. It's simple to configure for basic CRUD operations, and can handle creating drop boxes to force a field to comply with available values from another table. It also is set up to allow you to run just about any test you want on any field for which data is being entered, and, if necessary, populate another field based on the results of the test.

It cannot handle table joins, and the interface is pretty ugly in an aesthetic sense. If you want to try using it, go for it, but I'm already planning a complete replacement tool, so don't count on any repairs or revisions to this version.

