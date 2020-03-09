// Satone, Sandeep
// sxs6868
// 2019-11-22 
//----------------------------------------------------------
#include <stdio.h>

#include "cull.h"
#include "line.h"
#include "projection.h"
#include "triangle.h"
#include "vertex.h"
#include <math.h>

View *_view = NULL;

//----------------------------------------------------------
void cullProjectDumpTriangle( View *view, Projection *projection, Vertex *v1, Vertex *v2, Vertex *v3 )
{
  // TODO: If culling is active and the triangle should be culled,
  //       do nothing.  Otherwise project the vertices and dump
  //       the triangl.
  Vertex cameraPosition;
  cameraPosition.x = 0;
  cameraPosition.y = 0;
  if(view->m_cameraDistance != 0.0){
  	cameraPosition.z = view->m_cameraDistance;
  }  	
  else{
  	cameraPosition.z = +HUGE_VAL;
  }
	Vertex X,Y,Z;
	X.x = v1->x;
	X.y = v1->y;
	X.z = v1->z;
	
	Y.x = v2->x;
	Y.y = v2->y;
	Y.z = v2->z;
	
	Z.x = v3->x;
	Z.y = v3->y;
	Z.z = v3->z;

	
    Vertex *a = &X;
    Vertex *b = &Y;
    Vertex *c = &Z;


	
  	if(view->m_cull){  		
		if(cull(a,b,c,&cameraPosition)){
			projectVertex( projection, a, a);
			projectVertex( projection, b, b);
			projectVertex( projection, c, c);
			dumpTriangle( a, b, c );
		}
	}
	else{
			projectVertex( projection, a, a);
			projectVertex( projection, b, b);
			projectVertex( projection, c, c);
		dumpTriangle( a, b, c );
	}
}

//----------------------------------------------------------
void dumpTriangle( Vertex *v1, Vertex *v2, Vertex *v3 )
{
  Line l;

  l.p1X = v1->x;
  l.p1Y = v1->y;
  l.p2X = v2->x;
  l.p2Y = v2->y;

  if ( clipLine( _view, &l ) ) {
    dumpLine( &l );
  }

  l.p1X = v2->x;
  l.p1Y = v2->y;
  l.p2X = v3->x;
  l.p2Y = v3->y;

  if ( clipLine( _view, &l ) ) {
    dumpLine( &l );
  }

  l.p1X = v3->x;
  l.p1Y = v3->y;
  l.p2X = v1->x;
  l.p2Y = v1->y;

  if ( clipLine( _view, &l ) ) {
    dumpLine( &l );
  }
}

//----------------------------------------------------------
void setPortal( View *v )
{
  _view = v;
}

//----------------------------------------------------------

