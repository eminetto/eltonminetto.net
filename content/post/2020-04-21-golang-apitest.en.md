+++
title = "Testing APIs in Golang using apitest"
subtitle = ""
date = "2020-04-21T08:33:24+02:00"
bigimg = ""

+++
One advantage of the Go language is its standard library, which contains many useful features to develop modern applications, such as HTTP server and client, JSON parser, and tests. It is exactly this last point that I will talk about in this post.

With the standard library it is possible to write tests for your API, as in the following example.

## API code

In our `main.go` file, we will create a simple API:

```go
package main

import (
   "encoding/json"
   "log"
   "net/http"
   "os"
   "strconv"
   "time"

   "github.com/codegangsta/negroni"
   "github.com/gorilla/context"
   "github.com/gorilla/mux"
)

//Bookmark data
type Bookmark struct {
   ID   int    `json:"id"`
   Link string `json:"link"`
}

func main() {
   //router
   r := mux.NewRouter()
   //midllewares
   n := negroni.New(
      negroni.NewLogger(),
   )
   //routes
   r.Handle("/v1/bookmark", n.With(
      negroni.Wrap(bookmarkIndex()),
   )).Methods("GET", "OPTIONS").Name("bookmarkIndex")
   r.Handle("/v1/bookmark/{id}", n.With(
      negroni.Wrap(bookmarkFind()),
   )).Methods("GET", "OPTIONS").Name("bookmarkFind")
   http.Handle("/", r)
   //server
   logger := log.New(os.Stderr, "logger: ", log.Lshortfile)
   srv := &http.Server{
      ReadTimeout:  5 * time.Second,
      WriteTimeout: 10 * time.Second,
      Addr:         ":8080",
      Handler:      context.ClearHandler(http.DefaultServeMux),
      ErrorLog:     logger,
   }
   //start server
   err := srv.ListenAndServe()
   if err != nil {
      log.Fatal(err.Error())
   }
}

func bookmarkIndex() http.Handler {
   return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
      data := []*Bookmark{
         {
            ID:   1,
            Link: "http://google.com",
         },
         {
            ID:   2,
            Link: "https://apitest.dev",
         },
      }
      w.Header().Set("Content-Type", "application/json")
      if err := json.NewEncoder(w).Encode(data); err != nil {
         w.WriteHeader(http.StatusInternalServerError)
         w.Write([]byte("Error reading bookmarks"))
      }
   })
}

func bookmarkFind() http.Handler {
   return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
      vars := mux.Vars(r)
      id, err := strconv.Atoi(vars["id"])
      if err != nil {
         w.WriteHeader(http.StatusInternalServerError)
         w.Write([]byte("Error reading parameters"))
         return
      }
      data := []*Bookmark{
         {
            ID:   2,
            Link: "https://apitest.dev",
         },
      }
      if id != data[0].ID {
         w.WriteHeader(http.StatusNotFound)
         w.Write([]byte("Not found"))
         return
      }
      w.Header().Set("Content-Type", "application/json")
      if err := json.NewEncoder(w).Encode(data[0]); err != nil {
         w.WriteHeader(http.StatusInternalServerError)
         w.Write([]byte("Error reading bookmark"))
      }
   })
}
```


## Compiling

First, we need to start our project as a module, and install the external dependencies, such as *negroni* and *gorilla*. For this we execute the command:

```
go mod init github.com/eminetto/post-apitest
go: creating new go.mod: module github.com/eminetto/post-apitest
```


Now, running the build command the compilation process will install the dependencies:

```
go build
go: finding module for package github.com/gorilla/context
go: finding module for package github.com/gorilla/mux
go: finding module for package github.com/codegangsta/negroni
go: found github.com/codegangsta/negroni in github.com/codegangsta/negroni v1.0.0
go: found github.com/gorilla/context in github.com/gorilla/context v1.1.1
go: found github.com/gorilla/mux in github.com/gorilla/mux v1.7.4
```

## Testing with the standard library

We will now create the tests for this API. Our `main_test.go` file looks like this:

```go
package main

import (
   "net/http"
   "net/http/httptest"
   "testing"
   
   "github.com/gorilla/mux"
)

func Test_bookmarkIndex(t *testing.T) {
   r := mux.NewRouter()
   r.Handle("/v1/bookmark", bookmarkIndex())
   ts := httptest.NewServer(r)
   defer ts.Close()
   res, err := http.Get(ts.URL + "/v1/bookmark")
   if err != nil {
      t.Errorf("Expected nil, received %s", err.Error())
   }
   if res.StatusCode != http.StatusOK {
      t.Errorf("Expected %d, received %d", http.StatusOK, res.StatusCode)
   }
}

func Test_bookmarkFind(t *testing.T) {
   r := mux.NewRouter()
   r.Handle("/v1/bookmark/{id}", bookmarkFind())
   ts := httptest.NewServer(r)
   defer ts.Close()
   t.Run("not found", func(t *testing.T) {
      res, err := http.Get(ts.URL + "/v1/bookmark/1")
      if err != nil {
         t.Errorf("Expected nil, received %s", err.Error())
      }
      if res.StatusCode != http.StatusNotFound {
         t.Errorf("Expected %d, received %d", http.StatusNotFound, res.StatusCode)
      }
   })
   t.Run("found", func(t *testing.T) {
      res, err := http.Get(ts.URL + "/v1/bookmark/2")
      if err != nil {
         t.Errorf("Expected nil, received %s", err.Error())
      }
      if res.StatusCode != http.StatusOK {
         t.Errorf("Expected %d, received %d", http.StatusOK, res.StatusCode)
      }
   })
}
``` 

Running the tests, we see that everything is passing successfully:

```
go test -v
=== RUN   Test_bookmarkIndex
--- PASS: Test_bookmarkIndex (0.00s)
=== RUN   Test_bookmarkFind
=== RUN   Test_bookmarkFind/not_found
=== RUN   Test_bookmarkFind/found
--- PASS: Test_bookmarkFind (0.00s)
    --- PASS: Test_bookmarkFind/not_found (0.00s)
    --- PASS: Test_bookmarkFind/found (0.00s)
PASS
ok  	github.com/eminetto/post-apitest	0.371s
```

That way, we can test our API using only the standard library. But the tests code aren’t so readable, especially when we are testing a large API, with several *endpoints*.

## Using apitest

To improve our test code, we can use some third-party libraries, such as [apitest](https://apitest.dev), which simplifies the process.

Let’s start by installing the new packages. At the terminal, we execute:

```
go get github.com/steinfletcher/apitest
go: github.com/steinfletcher/apitest upgrade => v1.4.5
```

and

```
go get github.com/steinfletcher/apitest-jsonpath
go: github.com/steinfletcher/apitest-jsonpath upgrade => v1.5.0
```

Now let’s alter the `main_test.go` file:

```go
package main

import (
   "net/http"
   "net/http/httptest"
   "testing"

   "github.com/gorilla/mux"
   "github.com/steinfletcher/apitest"
   jsonpath "github.com/steinfletcher/apitest-jsonpath"
)

func Test_bookmarkIndex(t *testing.T) {
   r := mux.NewRouter()
   r.Handle("/v1/bookmark", bookmarkIndex())
   ts := httptest.NewServer(r)
   defer ts.Close()
   apitest.New().
      Handler(r).
      Get("/v1/bookmark").
      Expect(t).
      Status(http.StatusOK).
      End()
}

func Test_bookmarkFind(t *testing.T) {
   r := mux.NewRouter()
   r.Handle("/v1/bookmark/{id}", bookmarkFind())
   ts := httptest.NewServer(r)
   defer ts.Close()
   t.Run("not found", func(t *testing.T) {
      apitest.New().
         Handler(r).
         Get("/v1/bookmark/1").
         Expect(t).
         Status(http.StatusNotFound).
         End()
   })
   t.Run("found", func(t *testing.T) {
      apitest.New().
         Handler(r).
         Get("/v1/bookmark/2").
         Expect(t).
         Assert(jsonpath.Equal(`$.link`, "https://apitest.dev")).
         Status(http.StatusOK).
         End()
   })
}
```

The tests became much more readable, and we gained the functionality to test the resulting JSON. A note: it is also possible to test the resulting JSON using only the standard library, but we need to add a few more lines in the test.

In the [documentation](https://apitest.dev) you can see how powerful the library is, allowing advanced settings for *headers*, *cookies*, *debug* and *mocks*. It is worth taking the time to study the options and see the examples provided.


## Reports

An interesting feature that I would like to show in this post is reports generation. We need to make a slight change in the code, adding the line `Report(apitest.SequenceDiagram())` in the tests, as in the example:


```go
apitest.New().
   Report(apitest.SequenceDiagram()).
   Handler(r).
   Get("/v1/bookmark").
   Expect(t).
   Status(http.StatusOK).
   End()
```	

And when we run the tests again, we have the following result:

```
go test -v
=== RUN   Test_bookmarkIndex
Created sequence diagram (3157381659_2166136261.html): /Users/eminetto/Projects/post-apitest/.sequence/3157381659_2166136261.html
--- PASS: Test_bookmarkIndex (0.00s)
=== RUN   Test_bookmarkFind
=== RUN   Test_bookmarkFind/not_found
Created sequence diagram (1543772695_2166136261.html): /Users/eminetto/Projects/post-apitest/.sequence/1543772695_2166136261.html
=== RUN   Test_bookmarkFind/found
Created sequence diagram (1560550314_2166136261.html): /Users/eminetto/Projects/post-apitest/.sequence/1560550314_2166136261.html
--- PASS: Test_bookmarkFind (0.00s)
    --- PASS: Test_bookmarkFind/not_found (0.00s)
    --- PASS: Test_bookmarkFind/found (0.00s)
PASS
ok  	github.com/eminetto/post-apitest	0.296s
```

These are the generated reports:

[![apitest1](https://eltonminetto.dev/images/posts/apitest1.png)](https://eltonminetto.dev/images/posts/apitest1.png) 

[![apitest2](https://eltonminetto.dev/images/posts/apitest2.png)](https://eltonminetto.dev/images/posts/apitest2.png) 


## Is it worth using?

This is a question that has no single answer. Using only the standard library, the project gains speed in the execution of tests, besides not depending on third-party libraries, which can be a problem in some teams. 

With a library like apitest you gain in productivity and ease of maintenance, but you lose in speed of execution. A note on speed: I ran just a few simple tests and benchmarks, so I can’t say for sure how big is the difference compared to the standard library, but an overhead is visible.

Each team can make its benchmarks and make that decision, but most of the time I believe that the team’s productivity will gain several points in this choice.